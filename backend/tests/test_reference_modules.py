"""Backend tests for AdminKit reference modules (institutions/products/installments/methods).

Auth: Laravel session guard. Inertia page props are read from the `data-page`
attribute of the rendered HTML.
"""
import html
import json
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

EMAIL = "sa@bprbangunarta.co.id"
PASSWORD = "SA@4dm1n"

SLUGS = {
    "institutions": ("Data Instansi", ["code", "name"]),
    "products": ("Data Produk", ["code", "alias", "name"]),
    "installments": ("Sistem Cicilan", ["code", "name"]),
    "methods": ("Sistem Bunga", ["code", "name"]),
}


def page_props(resp):
    m = re.search(r'data-page="app"[^>]*>(.*?)</script>', resp.text, re.S)
    if not m:
        m = re.search(r'data-page="(\{.*?\})"\s*>', resp.text, re.S)
        return json.loads(html.unescape(m.group(1))) if m else None
    return json.loads(m.group(1))


class Client:
    def __init__(self):
        self.s = requests.Session()

    def token(self):
        raw = self.s.cookies.get("XSRF-TOKEN")
        return urllib.parse.unquote(raw) if raw else ""

    def get(self, path, **kw):
        return self.s.get(f"{BASE_URL}{path}", timeout=30, **kw)

    def send(self, method, path, data=None, referer="/"):
        headers = {
            "X-XSRF-TOKEN": self.token(),
            "Referer": f"{BASE_URL}{referer}",
            "Accept": "text/html, application/xhtml+xml",
        }
        return self.s.request(
            method, f"{BASE_URL}{path}", data=data, headers=headers, timeout=30, allow_redirects=True
        )


@pytest.fixture(scope="session")
def client():
    c = Client()
    c.get("/login")
    r = c.send("POST", "/login", {"credential": EMAIL, "password": PASSWORD}, referer="/login")
    if r.status_code != 200 or "/login" in r.url:
        props = page_props(r) or {}
        pytest.fail(f"Login failed: {r.status_code} url={r.url} errors={props.get('props', {}).get('errors')}")
    return c


@pytest.fixture(scope="session", autouse=True)
def cleanup(client):
    yield
    for slug in SLUGS:
        r = client.get(f"/{slug}?search=TEST&per_page=100")
        props = page_props(r)
        rows = props["props"]["records"]["data"] if props else []
        ids = [row["id"] for row in rows if str(row.get("code", "")).startswith("TEST")]
        if ids:
            client.send("POST", f"/{slug}/bulk", {f"ids[{i}]": v for i, v in enumerate(ids)}, referer=f"/{slug}")


# ── Page load / permissions ──────────────────────────────────────────────
@pytest.mark.parametrize("slug", list(SLUGS))
def test_index_page_loads(client, slug):
    r = client.get(f"/{slug}")
    assert r.status_code == 200, r.text[:300]
    props = page_props(r)
    assert props["component"] == "Reference"
    p = props["props"]
    assert p["slug"] == slug
    assert p["title"] == SLUGS[slug][0]
    assert [f["key"] for f in p["fields"]] == SLUGS[slug][1]
    assert "data" in p["records"] and "meta" in p["records"]
    for row in p["records"]["data"]:
        assert "_id" not in row


def test_permissions_matrix_contains_new_modules(client):
    r = client.get("/roles")
    assert r.status_code == 200
    props = page_props(r)
    blob = json.dumps(props["props"])
    for slug, (label, _) in SLUGS.items():
        assert f"{slug}.view" in blob, f"{slug}.view missing in roles matrix"
        assert f"{slug}.manage" in blob
        assert label in blob


# ── CRUD ─────────────────────────────────────────────────────────────────
def create(client, slug, values):
    return client.send("POST", f"/{slug}", values, referer=f"/{slug}")


def find_row(client, slug, code):
    r = client.get(f"/{slug}?search={code}")
    rows = page_props(r)["props"]["records"]["data"]
    return next((row for row in rows if row["code"] == code), None)


def payload(slug, suffix, name="TEST Nama"):
    fields = SLUGS[slug][1]
    data = {"code": f"test{suffix}", "name": name}
    if "alias" in fields:
        data["alias"] = f"tal{suffix}"
    return data


@pytest.mark.parametrize("slug", list(SLUGS))
def test_create_uppercases_and_persists(client, slug):
    data = payload(slug, "c1", name="TEST Satu")
    r = create(client, slug, data)
    assert r.status_code == 200, r.status_code
    props = page_props(r)["props"]
    assert not props.get("errors"), props.get("errors")

    row = find_row(client, slug, data["code"].upper())
    assert row is not None, f"row not persisted for {slug}"
    assert row["code"] == data["code"].upper()
    assert row["name"] == "TEST Satu"
    if "alias" in SLUGS[slug][1]:
        assert row["alias"] == data["alias"].upper()


@pytest.mark.parametrize("slug", list(SLUGS))
def test_validation_required_fields(client, slug):
    fields = SLUGS[slug][1]
    r = create(client, slug, {k: "" for k in fields})
    errors = page_props(r)["props"].get("errors", {})
    for f in fields:
        assert f in errors, f"missing required error for {slug}.{f}: {errors}"


@pytest.mark.parametrize("slug", list(SLUGS))
def test_duplicate_code_rejected(client, slug):
    base = payload(slug, "dup", name="TEST Dup")
    create(client, slug, base)
    dupe = dict(base)
    dupe["name"] = "TEST Dup Lain"
    if "alias" in dupe:
        dupe["alias"] = "taldup2"
    r = create(client, slug, dupe)
    errors = page_props(r)["props"].get("errors", {})
    assert "code" in errors, f"duplicate code not rejected for {slug}: {errors}"


def test_duplicate_alias_rejected(client):
    create(client, "products", {"code": "testal1", "alias": "testalias1", "name": "TEST Alias"})
    r = create(client, "products", {"code": "testal2", "alias": "testalias1", "name": "TEST Alias 2"})
    errors = page_props(r)["props"].get("errors", {})
    assert "alias" in errors, f"duplicate alias not rejected: {errors}"


def test_duplicate_name_should_be_allowed(client):
    """Spec: only code (and alias for products) must be unique; name is free text."""
    create(client, "institutions", {"code": "testn1", "name": "TEST Nama Sama"})
    r = create(client, "institutions", {"code": "testn2", "name": "TEST Nama Sama"})
    errors = page_props(r)["props"].get("errors", {})
    assert "name" not in errors, f"name wrongly enforced unique: {errors}"


@pytest.mark.parametrize("slug", list(SLUGS))
def test_update_and_delete(client, slug):
    data = payload(slug, "up1", name="TEST Awal")
    create(client, slug, data)
    row = find_row(client, slug, data["code"].upper())
    assert row is not None

    upd = dict(data)
    upd["name"] = "TEST Diubah"
    r = client.send("PUT", f"/{slug}/{row['id']}", upd, referer=f"/{slug}")
    assert r.status_code == 200
    assert not page_props(r)["props"].get("errors"), page_props(r)["props"].get("errors")
    after = find_row(client, slug, data["code"].upper())
    assert after["name"] == "TEST Diubah"

    r = client.send("DELETE", f"/{slug}/{row['id']}", referer=f"/{slug}")
    assert r.status_code == 200
    assert find_row(client, slug, data["code"].upper()) is None


def test_bulk_delete(client):
    slug = "installments"
    ids = []
    for i in range(3):
        create(client, slug, {"code": f"testbulk{i}", "name": f"TEST Bulk {i}"})
        row = find_row(client, slug, f"TESTBULK{i}")
        assert row is not None
        ids.append(row["id"])

    r = client.send("POST", f"/{slug}/bulk", {f"ids[{i}]": v for i, v in enumerate(ids)}, referer=f"/{slug}")
    assert r.status_code == 200
    for i in range(3):
        assert find_row(client, slug, f"TESTBULK{i}") is None


def test_bulk_requires_ids(client):
    r = client.send("POST", "/methods/bulk", {}, referer="/methods")
    errors = page_props(r)["props"].get("errors", {})
    assert "ids" in errors, errors


# ── Search / sort / pagination ───────────────────────────────────────────
def test_search_sort_pagination(client):
    slug = "methods"
    for i in range(12):
        create(client, slug, {"code": f"testpg{i:02d}", "name": f"TEST Page {i:02d}"})

    r = client.get(f"/{slug}?search=TESTPG&per_page=10&sort=code&dir=asc")
    p = page_props(r)["props"]
    assert len(p["records"]["data"]) == 10
    assert p["records"]["meta"]["total"] >= 12
    codes = [row["code"] for row in p["records"]["data"]]
    assert codes == sorted(codes)

    r2 = client.get(f"/{slug}?search=TESTPG&per_page=10&page=2&sort=code&dir=asc")
    p2 = page_props(r2)["props"]
    assert len(p2["records"]["data"]) >= 2
    assert set(row["id"] for row in p2["records"]["data"]).isdisjoint(row["id"] for row in p["records"]["data"])

    r3 = client.get(f"/{slug}?search=TESTPG&per_page=10&sort=code&dir=desc")
    codes_desc = [row["code"] for row in page_props(r3)["props"]["records"]["data"]]
    assert codes_desc == sorted(codes_desc, reverse=True)

    r4 = client.get(f"/{slug}?search=TESTPG05")
    rows = page_props(r4)["props"]["records"]["data"]
    assert len(rows) == 1 and rows[0]["code"] == "TESTPG05"


# ── Audit trail ──────────────────────────────────────────────────────────
def test_audit_trail_records_reference_activity(client):
    create(client, "institutions", {"code": "testaudit", "name": "TEST Audit"})
    r = client.get("/audit-trail?search=TESTAUDIT")
    assert r.status_code == 200
    blob = json.dumps(page_props(r)["props"])
    assert "Data Instansi" in blob, "module label missing in audit trail"
    assert "TESTAUDIT" in blob


# ── Regression: existing pages still load ────────────────────────────────
@pytest.mark.parametrize("path", ["/", "/users", "/users/create", "/menus", "/permissions", "/roles", "/appearance", "/object-storage", "/audit-trail", "/profile"])
def test_existing_pages_load(client, path):
    r = client.get(path)
    assert r.status_code == 200, f"{path} -> {r.status_code}"
    assert page_props(r) is not None
