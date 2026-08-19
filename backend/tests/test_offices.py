"""Backend tests: Data Kantor (offices) CRUD + regression for reference pages,
committees export and cosmetic fixes (SIPEBRI / AdminKit Laravel app)."""

import json
import os
import re
import urllib.parse
from pathlib import Path

import pytest
import requests
from dotenv import dotenv_values

frontend_env = dotenv_values("/app/frontend/.env")
base_url = os.environ.get("REACT_APP_BACKEND_URL") or frontend_env.get("REACT_APP_BACKEND_URL")
if not base_url:
    raise RuntimeError("REACT_APP_BACKEND_URL missing")
BASE_URL = base_url.rstrip("/")

BUILTIN_OFFICE_CODES = {"00", "01", "02", "03", "04", "05", "06"}


def credentials():
    content = Path("/app/memory/test_credentials.md").read_text(encoding="utf-8")
    email = re.search(r"`(sa@[^`]+)`", content)
    pwd = re.search(r"`(SA@[^`]+)`", content)
    if not email or not pwd:
        pytest.skip("credentials not found")
    return email.group(1), pwd.group(1)


def parse_page(text):
    """Inertia page object may be a JSON script tag or a data-page attribute."""
    import html as htmlmod

    m = re.search(r'data-page="app"[^>]*>(.*?)</script>', text, re.S)
    if m:
        return json.loads(m.group(1))
    m = re.search(r'data-page="(\{.*?\})"\s*>', text, re.S)
    if m:
        return json.loads(htmlmod.unescape(m.group(1)))
    m = re.search(r'id="app"[^>]*data-page=\'(\{.*?\})\'', text, re.S)
    if m:
        return json.loads(m.group(1))
    return None


def page_props(session, path):
    """Fetch an Inertia page and return its props dict."""
    r = session.get(f"{BASE_URL}{path}", timeout=60)
    assert r.status_code == 200, f"{path} -> {r.status_code}"
    page = parse_page(r.text)
    assert page, f"page object not found on {path}: {r.text[:300]}"
    return page["props"]


def xsrf(session):
    token = session.cookies.get("XSRF-TOKEN")
    assert token, "XSRF-TOKEN cookie missing"
    return urllib.parse.unquote(token)


@pytest.fixture(scope="session")
def client():
    s = requests.Session()
    s.headers.update({"Accept": "text/html,application/xhtml+xml", "User-Agent": "pytest-qa"})
    email, pwd = credentials()
    s.get(f"{BASE_URL}/login", timeout=60)
    r = s.post(
        f"{BASE_URL}/login",
        data={"credential": email, "password": pwd},
        headers={"X-XSRF-TOKEN": xsrf(s), "Referer": f"{BASE_URL}/login"},
        timeout=60,
        allow_redirects=True,
    )
    if r.status_code != 200 or "/login" in r.url:
        pytest.fail(f"login failed: {r.status_code} {r.url}")
    return s


def post(client, path, payload, method="post"):
    fn = getattr(client, method)
    return fn(
        f"{BASE_URL}{path}",
        data=payload,
        headers={"X-XSRF-TOKEN": xsrf(client), "Referer": f"{BASE_URL}/offices"},
        timeout=60,
        allow_redirects=True,
    )


def offices(client):
    props = page_props(client, "/offices?per_page=100")
    return props["records"]["data"], props


# ── Modul: Data Kantor — daftar & seeder ──────────────────────────────────
class TestOfficesIndex:
    def test_index_has_seven_seeded_offices_sorted_by_code(self, client):
        rows, props = offices(client)
        assert props["title"] == "Data Kantor"
        assert props["slug"] == "offices"
        assert [f["key"] for f in props["fields"]] == ["code", "alias", "name"]
        alias_field = next(f for f in props["fields"] if f["key"] == "alias")
        assert alias_field.get("hide_below") == "sm"
        codes = [r["code"] for r in rows]
        assert BUILTIN_OFFICE_CODES.issubset(set(codes)), codes
        seeded = [r for r in rows if r["code"] in BUILTIN_OFFICE_CODES]
        assert len(seeded) == 7
        assert [r["code"] for r in seeded] == sorted(r["code"] for r in seeded)
        by_code = {r["code"]: (r["alias"], r["name"]) for r in seeded}
        assert by_code["00"] == ("PMK", "Pamanukan")
        assert by_code["06"] == ("PSK", "Pusakajaya")
        assert all("_id" not in r for r in rows)


# ── Modul: Data Kantor — CRUD ─────────────────────────────────────────────
class TestOfficesCrud:
    created = []

    @pytest.fixture(scope="class", autouse=True)
    def cleanup(self, client):
        yield
        rows, _ = offices(client)
        for r in rows:
            if r["code"] not in BUILTIN_OFFICE_CODES and r["code"].startswith("Z"):
                post(client, f"/offices/{r['id']}", {}, method="delete")

    def test_create_uppercases_code_and_alias(self, client):
        r = post(client, "/offices", {"code": "z9", "alias": "zqa", "name": "Kantor uji QA"})
        assert r.status_code == 200
        rows, _ = offices(client)
        rec = next((x for x in rows if x["code"] == "Z9"), None)
        assert rec is not None, "created office not persisted"
        assert rec["alias"] == "ZQA"
        assert rec["name"] == "Kantor uji QA"
        TestOfficesCrud.created.append(rec["id"])

    def test_duplicate_code_rejected(self, client):
        r = post(client, "/offices", {"code": "Z9", "alias": "ZDUP", "name": "Dup Kode"})
        props = parse_page(r.text)["props"]
        assert "code" in props.get("errors", {}), props.get("errors")

    def test_duplicate_alias_rejected(self, client):
        r = post(client, "/offices", {"code": "Z8", "alias": "ZQA", "name": "Dup Alias"})
        props = parse_page(r.text)["props"]
        assert "alias" in props.get("errors", {}), props.get("errors")

    def test_duplicate_name_allowed(self, client):
        r = post(client, "/offices", {"code": "Z7", "alias": "ZQB", "name": "Kantor uji QA"})
        assert r.status_code == 200
        rows, _ = offices(client)
        rec = next((x for x in rows if x["code"] == "Z7"), None)
        assert rec is not None and rec["name"] == "Kantor uji QA"
        TestOfficesCrud.created.append(rec["id"])

    def test_update_persists(self, client):
        rows, _ = offices(client)
        rec = next(x for x in rows if x["code"] == "Z7")
        r = post(client, f"/offices/{rec['id']}", {"code": "z6", "alias": "zqc", "name": "Kantor uji Ubah"}, method="put")
        assert r.status_code == 200
        rows, _ = offices(client)
        updated = next((x for x in rows if x["id"] == rec["id"]), None)
        assert updated == {"id": rec["id"], "code": "Z6", "alias": "ZQC", "name": "Kantor uji Ubah"}

    def test_delete_single(self, client):
        rows, _ = offices(client)
        rec = next(x for x in rows if x["code"] == "Z6")
        r = post(client, f"/offices/{rec['id']}", {}, method="delete")
        assert r.status_code == 200
        rows, _ = offices(client)
        assert all(x["id"] != rec["id"] for x in rows)

    def test_bulk_delete(self, client):
        for code, alias in [("Z1", "ZB1"), ("Z2", "ZB2")]:
            assert post(client, "/offices", {"code": code, "alias": alias, "name": f"Bulk {code}"}).status_code == 200
        rows, _ = offices(client)
        ids = [x["id"] for x in rows if x["code"] in ("Z1", "Z2", "Z9")]
        assert len(ids) == 3
        r = client.post(
            f"{BASE_URL}/offices/bulk",
            data={f"ids[{i}]": v for i, v in enumerate(ids)},
            headers={"X-XSRF-TOKEN": xsrf(client), "Referer": f"{BASE_URL}/offices"},
            timeout=60,
        )
        assert r.status_code == 200
        rows, _ = offices(client)
        assert not [x for x in rows if x["id"] in ids]
        assert len([x for x in rows if x["code"] in BUILTIN_OFFICE_CODES]) == 7

    def test_validation_requires_all_fields(self, client):
        r = post(client, "/offices", {"code": "", "alias": "", "name": ""})
        props = parse_page(r.text)["props"]
        assert set(props.get("errors", {})) >= {"code", "alias", "name"}


# ── Izin baru offices.view / offices.manage di matriks peranan ───────────
class TestPermissions:
    def test_offices_permission_in_role_matrix(self, client):
        props = page_props(client, "/roles/1")
        blob = json.dumps(props)
        assert "offices.view" in blob and "offices.manage" in blob
        assert "Data Kantor" in blob

    def test_permissions_page_lists_offices(self, client):
        props = page_props(client, "/permissions")
        assert "offices" in json.dumps(props)


# ── Regresi halaman lain + Komite (kondisi Capitalize) ───────────────────
class TestRegression:
    @pytest.mark.parametrize(
        "path",
        [
            "/institutions", "/products", "/installments", "/methods",
            "/committees", "/committees/2", "/users", "/users/create",
            "/menus", "/roles", "/permissions", "/audit-trail",
            "/appearance", "/profile", "/object-storage", "/offices",
        ],
    )
    def test_pages_render(self, client, path):
        r = client.get(f"{BASE_URL}{path}", timeout=60)
        assert r.status_code == 200, f"{path} -> {r.status_code}"

    def test_institutions_has_ten_rows(self, client):
        props = page_props(client, "/institutions?per_page=100")
        assert len(props["records"]["data"]) == 10

    def test_committees_has_19_paths(self, client):
        props = page_props(client, "/committees")
        assert len(props["paths"]) == 19, len(props["paths"])

    def test_committee_detail_title_capitalized(self, client):
        props = page_props(client, "/committees/2")
        title = props["path"]["title"]
        assert title and title == title.strip()
        words = [w for w in title.split() if w.isalpha() and len(w) > 3]
        # produk alias (mis. KRS) boleh HURUF BESAR, kondisi harus Capitalize
        assert not [w for w in words if w.isupper()], f"uppercase words in title: {title}"

    def test_flash_message_condition_capitalized(self, client):
        """PERBAIKAN 2: kondisi dikirim huruf kecil, disimpan HURUF BESAR,
        pesan flash memakai Capitalize."""
        r = client.post(
            f"{BASE_URL}/committees",
            data={"condition": "testkondisi", "mechanism": "plafon", "is_active": 1, "product_id": ""},
            headers={"X-XSRF-TOKEN": xsrf(client), "Referer": f"{BASE_URL}/committees"},
            timeout=60,
        )
        assert r.status_code == 200, r.status_code
        page = parse_page(r.text)
        flash = json.dumps(page["props"].get("flash") or page["props"].get("notify") or page["props"])
        assert "Testkondisi" in flash, flash[:400]
        assert "TESTKONDISI" not in flash, flash[:400]
        path_id = page["props"]["path"]["id"]
        assert page["props"]["path"]["condition"] == "TESTKONDISI"
        # cleanup
        d = client.delete(
            f"{BASE_URL}/committees/{path_id}",
            headers={"X-XSRF-TOKEN": xsrf(client), "Referer": f"{BASE_URL}/committees"},
            timeout=60,
        )
        assert d.status_code == 200
        del_flash = json.dumps(parse_page(d.text)["props"])
        assert "Testkondisi" in del_flash and "TESTKONDISI" not in del_flash, del_flash[:400]

    def test_committees_export_ok(self, client):
        r = client.get(f"{BASE_URL}/committees/export", timeout=120)
        assert r.status_code == 200
        assert len(r.content) > 1000
