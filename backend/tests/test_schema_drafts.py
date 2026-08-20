"""Tests: Skema Migrasi module (/schema-drafts) + Collateral detail (developer view).

Laravel + Inertia app at /app/adminkit. Uses real session auth (superadmin) and
Inertia JSON responses (X-Inertia header) to inspect page props.
"""
import os
import re
import urllib.parse

import pytest
import requests
from dotenv import dotenv_values

frontend_env = dotenv_values("/app/frontend/.env")
base_url = os.environ.get("REACT_APP_BACKEND_URL") or frontend_env.get("REACT_APP_BACKEND_URL")
if not base_url:
    raise RuntimeError("REACT_APP_BACKEND_URL missing")
BASE_URL = base_url.rstrip("/")

SUPERADMIN = ("superadmin", "SA@4dm1n")
STAFF = ("mohmuksin", "password")


def inertia_version():
    r = requests.get(f"{BASE_URL}/login", timeout=30)
    m = re.search(r'"version":"([a-f0-9]+)"', r.text)
    assert m, "cannot read inertia asset version"
    return m.group(1)


VERSION = inertia_version()
import glob as _glob
MIGRATION_FILES_AT_START = len(_glob.glob("/app/adminkit/database/migrations/*.php"))


def xsrf(session):
    token = session.cookies.get("XSRF-TOKEN")
    return urllib.parse.unquote(token) if token else ""


def login(username, password):
    s = requests.Session()
    s.headers.update({"Accept": "text/html, application/xhtml+xml", "X-Requested-With": "XMLHttpRequest"})
    s.get(f"{BASE_URL}/login", timeout=30)
    r = s.post(
        f"{BASE_URL}/login",
        data={"credential": username, "password": password},
        headers={"X-XSRF-TOKEN": xsrf(s), "X-Inertia": "true", "X-Inertia-Version": VERSION},
        allow_redirects=False,
        timeout=30,
    )
    if r.status_code not in (200, 302, 303, 409):
        pytest.fail(f"login failed {r.status_code}: {r.text[:400]}")
    return s


def inertia_get(session, path, expect=200):
    r = session.get(
        f"{BASE_URL}{path}",
        headers={"X-Inertia": "true", "X-Inertia-Version": VERSION, "Accept": "text/html, application/xhtml+xml"},
        timeout=30,
        allow_redirects=False,
    )
    assert r.status_code == expect, f"GET {path} -> {r.status_code} (expected {expect}) {r.text[:300]}"
    if r.status_code == 200 and "application/json" in r.headers.get("content-type", ""):
        return r.json()["props"]
    return {}


def send(session, method, path, data=None, expect=(302, 303)):
    r = session.request(
        method,
        f"{BASE_URL}{path}",
        json=data or {},
        headers={
            "X-XSRF-TOKEN": xsrf(session),
            "X-Inertia": "true",
            "X-Inertia-Version": VERSION,
            "Accept": "text/html, application/xhtml+xml",
        },
        allow_redirects=False,
        timeout=30,
    )
    assert r.status_code in expect, f"{method} {path} -> {r.status_code} {r.text[:400]}"
    return r


@pytest.fixture(scope="session")
def admin():
    s = login(*SUPERADMIN)
    props = inertia_get(s, "/schema-drafts")
    assert "drafts" in props, "superadmin cannot reach /schema-drafts"
    return s


# ---------------------------------------------------------------- schema drafts
class TestSchemaDraftListing:
    def test_index_lists_seeded_drafts(self, admin):
        drafts = {d["table_name"]: d for d in inertia_get(admin, "/schema-drafts")["drafts"]}
        assert "collateral_simulations" in drafts
        assert "credit_applications" in drafts
        assert drafts["collateral_simulations"]["table_exists"] is True
        assert drafts["credit_applications"]["table_exists"] is False
        assert drafts["collateral_simulations"]["columns_count"] > 0

    def test_existing_table_diff_all_same(self, admin):
        props = inertia_get(admin, "/schema-drafts/1")
        assert props["draft"]["table_exists"] is True
        statuses = {row["status"] for row in props["diff"]}
        assert statuses == {"sama"}, f"expected all 'sama', got {statuses}"

    def test_existing_table_migration_uses_schema_table(self, admin):
        code = inertia_get(admin, "/schema-drafts/1")["migration"]["code"]
        assert "Schema::table('collateral_simulations'" in code
        assert "Schema::create" not in code

    def test_new_table_migration_uses_schema_create(self, admin):
        props = inertia_get(admin, "/schema-drafts/2")
        code = props["migration"]["code"]
        assert "Schema::create('credit_applications'" in code
        assert "$table->id();" in code
        assert "$table->timestamps();" in code
        assert props["migration"]["file"].endswith("create_credit_applications_table.php")
        assert {r["status"] for r in props["diff"]} == {"baru"}


class TestDraftValidation:
    def test_uppercase_table_name_rejected(self, admin):
        r = send(admin, "POST", "/schema-drafts", {"name": "TEST_Invalid", "table_name": "Test Table"}, expect=(302, 303, 422))
        if r.status_code == 422:
            assert "table_name" in r.json().get("errors", {})

    def test_duplicate_table_name_rejected(self, admin):
        r = send(admin, "POST", "/schema-drafts", {"name": "TEST_Dup", "table_name": "collateral_simulations"}, expect=(302, 303, 422))
        if r.status_code == 422:
            assert "table_name" in r.json().get("errors", {})

    def test_validation_errors_surface_in_session(self, admin):
        send(admin, "POST", "/schema-drafts", {"name": "TEST_Bad", "table_name": "Bad Name"}, expect=(302, 303, 422))
        props = inertia_get(admin, "/schema-drafts")
        errors = props.get("errors") or {}
        assert "table_name" in errors, f"no table_name error shared to page: {errors}"


class TestDraftLifecycle:
    draft_id = None

    def test_01_create_draft(self, admin):
        r = send(admin, "POST", "/schema-drafts", {
            "name": "TEST_Rancangan QA",
            "table_name": "test_qa_designs",
            "note": "dibuat oleh tes otomatis",
            "with_id": True,
            "with_timestamps": True,
            "with_soft_deletes": False,
        })
        location = r.headers.get("Location", "") or r.headers.get("X-Inertia-Location", "")
        m = re.search(r"/schema-drafts/(\d+)", location)
        assert m, f"store did not redirect to draft page: {location}"
        TestDraftLifecycle.draft_id = int(m.group(1))
        props = inertia_get(admin, f"/schema-drafts/{TestDraftLifecycle.draft_id}")
        assert props["draft"]["table_name"] == "test_qa_designs"
        assert props["draft"]["table_exists"] is False
        assert props["columns"] == []

    def test_02_add_columns(self, admin):
        did = TestDraftLifecycle.draft_id
        send(admin, "POST", f"/schema-drafts/{did}/columns", {
            "name": "kode", "type": "string", "length": "20",
            "is_nullable": False, "is_unique": True, "is_index": False, "comment": "kode unik",
        })
        send(admin, "POST", f"/schema-drafts/{did}/columns", {
            "name": "jumlah", "type": "integer", "is_nullable": True, "default_value": "0",
        })
        props = inertia_get(admin, f"/schema-drafts/{did}")
        names = [c["name"] for c in props["columns"]]
        assert names == ["kode", "jumlah"], names
        code = props["migration"]["code"]
        assert "$table->string('kode', 20)->unique()->comment('kode unik');" in code, code
        assert "$table->integer('jumlah')->nullable()->default(0);" in code, code

    def test_03_invalid_column_name_rejected(self, admin):
        did = TestDraftLifecycle.draft_id
        send(admin, "POST", f"/schema-drafts/{did}/columns", {"name": "Bad Name", "type": "string"}, expect=(302, 303, 422))
        props = inertia_get(admin, f"/schema-drafts/{did}")
        assert "name" in (props.get("errors") or {})
        assert len(props["columns"]) == 2

    def test_04_duplicate_column_rejected(self, admin):
        did = TestDraftLifecycle.draft_id
        send(admin, "POST", f"/schema-drafts/{did}/columns", {"name": "kode", "type": "string"}, expect=(302, 303, 422))
        props = inertia_get(admin, f"/schema-drafts/{did}")
        assert len(props["columns"]) == 2

    def test_05_update_column(self, admin):
        did = TestDraftLifecycle.draft_id
        props = inertia_get(admin, f"/schema-drafts/{did}")
        col = next(c for c in props["columns"] if c["name"] == "jumlah")
        send(admin, "PUT", f"/schema-drafts/{did}/columns/{col['id']}", {
            "name": "jumlah_total", "type": "decimal", "length": "15, 2", "is_nullable": False,
        })
        props = inertia_get(admin, f"/schema-drafts/{did}")
        updated = next((c for c in props["columns"] if c["id"] == col["id"]), None)
        assert updated and updated["name"] == "jumlah_total"
        assert updated["type"] == "decimal" and updated["length"] == "15, 2"
        assert "$table->decimal('jumlah_total', 15, 2)" in props["migration"]["code"]

    def test_06_reorder_persists(self, admin):
        did = TestDraftLifecycle.draft_id
        cols = inertia_get(admin, f"/schema-drafts/{did}")["columns"]
        reversed_ids = [c["id"] for c in cols][::-1]
        send(admin, "PUT", f"/schema-drafts/{did}/reorder", {"ids": reversed_ids})
        after = [c["id"] for c in inertia_get(admin, f"/schema-drafts/{did}")["columns"]]
        assert after == reversed_ids, f"order not persisted: {after} vs {reversed_ids}"

    def test_07_reorder_rejects_foreign_ids(self, admin):
        did = TestDraftLifecycle.draft_id
        foreign_id = inertia_get(admin, "/schema-drafts/1")["columns"][0]["id"]
        send(admin, "PUT", f"/schema-drafts/{did}/reorder", {"ids": [foreign_id]}, expect=(302, 303, 422))

    def test_08_column_route_is_scoped_to_draft(self, admin):
        """Deleting another draft's column through this draft must not succeed."""
        did = TestDraftLifecycle.draft_id
        other = inertia_get(admin, "/schema-drafts/1")
        victim = other["columns"][0]
        send(admin, "DELETE", f"/schema-drafts/{did}/columns/{victim['id']}", expect=(403, 404))
        still = [c["id"] for c in inertia_get(admin, "/schema-drafts/1")["columns"]]
        assert victim["id"] in still, "column of another draft was deleted (unscoped binding)"

    def test_09_delete_column(self, admin):
        did = TestDraftLifecycle.draft_id
        cols = inertia_get(admin, f"/schema-drafts/{did}")["columns"]
        target = next(c for c in cols if c["name"] == "kode")
        send(admin, "DELETE", f"/schema-drafts/{did}/columns/{target['id']}")
        names = [c["name"] for c in inertia_get(admin, f"/schema-drafts/{did}")["columns"]]
        assert "kode" not in names

    def test_10_import_requires_existing_table(self, admin):
        did = TestDraftLifecycle.draft_id
        send(admin, "POST", f"/schema-drafts/{did}/import")
        props = inertia_get(admin, f"/schema-drafts/{did}")
        assert len(props["columns"]) == 1, "import should not change draft when table missing"

    def test_11_delete_draft(self, admin):
        did = TestDraftLifecycle.draft_id
        send(admin, "DELETE", f"/schema-drafts/{did}")
        inertia_get(admin, f"/schema-drafts/{did}", expect=404)


class TestDiffAndImport:
    def test_new_column_marked_baru_then_dihapus(self, admin):
        """Add a column to the collateral_simulations draft -> 'baru'; remove an
        existing one -> 'dihapus'. Restores state via import at the end."""
        send(admin, "POST", "/schema-drafts/1/columns", {"name": "test_qa_kolom", "type": "string"})
        props = inertia_get(admin, "/schema-drafts/1")
        row = next((r for r in props["diff"] if r["name"] == "test_qa_kolom"), None)
        assert row and row["status"] == "baru", props["diff"]
        assert "$table->string('test_qa_kolom');" in props["migration"]["code"]

        col = next(c for c in props["columns"] if c["name"] == "test_qa_kolom")
        send(admin, "DELETE", f"/schema-drafts/1/columns/{col['id']}")

        # remove a real column from the draft -> should show as 'dihapus'
        props = inertia_get(admin, "/schema-drafts/1")
        real = next(c for c in props["columns"] if c["name"] == "region_label")
        send(admin, "DELETE", f"/schema-drafts/1/columns/{real['id']}")
        props = inertia_get(admin, "/schema-drafts/1")
        row = next((r for r in props["diff"] if r["name"] == "region_label"), None)
        assert row and row["status"] == "dihapus", props["diff"]
        code = props["migration"]["code"]
        assert "dropColumn(" in code and "'region_label'" in code, code

    def test_import_restores_columns_from_real_table(self, admin):
        send(admin, "POST", "/schema-drafts/1/import")
        props = inertia_get(admin, "/schema-drafts/1")
        names = [c["name"] for c in props["columns"]]
        assert "region_label" in names
        for dropped in ["paripasu", "file_number", "auto_number", "ownership", "owner_same_as_cif", "region_id"]:
            assert dropped not in names
        assert {r["status"] for r in props["diff"]} == {"sama"}, props["diff"]


class TestAuthorization:
    def test_staff_without_permission_gets_403(self):
        s = login(*STAFF)
        r = s.get(f"{BASE_URL}/schema-drafts", headers={"X-Inertia": "true", "X-Inertia-Version": VERSION}, allow_redirects=False, timeout=30)
        assert r.status_code == 403, f"expected 403, got {r.status_code}"

    def test_staff_cannot_write(self):
        s = login(*STAFF)
        r = s.post(
            f"{BASE_URL}/schema-drafts",
            json={"name": "TEST_x", "table_name": "test_x"},
            headers={"X-XSRF-TOKEN": xsrf(s), "X-Inertia": "true", "X-Inertia-Version": VERSION},
            allow_redirects=False,
            timeout=30,
        )
        assert r.status_code == 403, r.status_code

    def test_guest_redirected_to_login(self):
        s = requests.Session()
        r = s.get(f"{BASE_URL}/schema-drafts", allow_redirects=False, timeout=30)
        assert r.status_code in (302, 303) and "/login" in r.headers.get("Location", "")


# ------------------------------------------------------- collateral simulations
class TestCollateralDetail:
    DROPPED = ["paripasu", "file_number", "auto_number", "ownership", "owner_same_as_cif", "region_id"]

    def test_detail_columns_exclude_dropped(self, admin):
        rec = inertia_get(admin, "/collateral-simulation")["records"]["data"][0]
        props = inertia_get(admin, f"/collateral-simulation/{rec['id']}")
        names = [c["name"] for c in props["columns"]]
        for dropped in self.DROPPED:
            assert dropped not in names, f"{dropped} still present"
        assert "collateral_id" in names and "region_code" in names
        col = next(c for c in props["columns"] if c["name"] == "collateral_id")
        assert set(col) >= {"name", "type", "nullable", "default", "value"}

    def test_payload_keys(self, admin):
        rec = inertia_get(admin, "/collateral-simulation")["records"]["data"][0]
        payload = inertia_get(admin, f"/collateral-simulation/{rec['id']}")["payload"]
        assert payload["no_rek"] == "", payload.get("no_rek")
        assert payload["kepemilikan"] == "", payload.get("kepemilikan")
        for dropped in self.DROPPED:
            assert dropped not in payload

    def test_detail_404_for_missing(self, admin):
        inertia_get(admin, "/collateral-simulation/99999", expect=404)


class TestCollateralWrites:
    created_id = None

    def test_01_create(self, admin):
        refs = inertia_get(admin, "/collateral-simulation/create")
        payload = {
            "collateral_id": "TEST-QA-001",
            "collateral_type_code": refs["collateralTypes"][0]["value"],
            "binding_type_code": refs["bindingTypes"][0]["value"],
            "document_number": "TEST/QA/001",
            "description": "TEST agunan qa",
            "owner_name": "TEST pemilik",
            "owner_address": "Jl. Tes 1",
            "region_code": refs["regionOptions"][0]["value"],
            "region_label": refs["regionOptions"][0]["label"],
        }
        send(admin, "POST", "/collateral-simulation", payload)
        rows = inertia_get(admin, "/collateral-simulation?search=TEST-QA-001")["records"]["data"]
        assert rows, "created record not found"
        row = rows[0]
        TestCollateralWrites.created_id = row["id"]
        assert row["owner_name"] == "TEST PEMILIK"
        assert row["insured"] == "T" and row["ppap_code"] == "1"

    def test_02_update(self, admin):
        rid = TestCollateralWrites.created_id
        refs = inertia_get(admin, f"/collateral-simulation/{rid}/edit")
        rec = refs["record"]
        payload = {
            **{k: v for k, v in rec.items() if k not in ("payload", "id")},
            "condition_code": refs["conditions"][0]["value"],
            "condition_date": "2026-07-01",
            "insured": "Y",
            "insurance_start_date": "2026-07-02",
            "value_guarantee": 500000000,
            "value_fair": 450000000,
            "value_appraisal": 400000000,
            "appraised_at": "2026-07-03",
        }
        send(admin, "PUT", f"/collateral-simulation/{rid}", payload)
        props = inertia_get(admin, f"/collateral-simulation/{rid}")
        values = {c["name"]: c["value"] for c in props["columns"]}
        assert str(values["value_guarantee"]) == "500000000", values["value_guarantee"]
        assert values["insured"] == "Y"
        assert str(values["condition_date"]).startswith("2026-07-01")

    def test_03_cleanup(self, admin):
        rid = TestCollateralWrites.created_id
        send(admin, "DELETE", f"/collateral-simulation/{rid}")
        inertia_get(admin, f"/collateral-simulation/{rid}", expect=404)


class TestNoSchemaSideEffects:
    """Skema Migrasi is preview-only: no migration files, no schema changes."""

    def test_no_new_migration_files(self):
        import glob
        files = glob.glob("/app/adminkit/database/migrations/*.php")
        assert len(files) == MIGRATION_FILES_AT_START, f"migration files changed: {len(files)} vs {MIGRATION_FILES_AT_START}"
        assert not [f for f in files if "test_qa_designs" in f or "credit_applications" in f]

    def test_draft_tables_not_created(self, admin):
        drafts = inertia_get(admin, "/schema-drafts")["drafts"]
        pending = [d for d in drafts if d["table_name"] == "credit_applications"]
        assert pending and pending[0]["table_exists"] is False
