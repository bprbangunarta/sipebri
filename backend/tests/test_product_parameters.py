"""Backend tests: Parameter Produk (SK Direksi) — /products/{id} & PUT /products/{id}/parameters."""
import os
import re
import urllib.parse
from pathlib import Path

import pytest
import requests
from dotenv import dotenv_values

env = dotenv_values("/app/frontend/.env")
BASE_URL = (os.environ.get("REACT_APP_BACKEND_URL") or env.get("REACT_APP_BACKEND_URL") or "").rstrip("/")
if not BASE_URL:
    raise RuntimeError("REACT_APP_BACKEND_URL missing")


def creds():
    content = Path("/app/memory/test_credentials.md").read_text(encoding="utf-8")
    m = re.search(r"`(sa@[^`]+)`", content)
    p = re.search(r"`(SA@[^`]+)`", content)
    if not m or not p:
        pytest.skip("credentials not found")
    return m.group(1), p.group(1)


def parse_page(text):
    import html as htmlmod, json
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


VERSION = {"v": ""}


def xsrf(session):
    tok = session.cookies.get("XSRF-TOKEN")
    return urllib.parse.unquote(tok) if tok else ""


@pytest.fixture(scope="session")
def client():
    s = requests.Session()
    s.headers.update({"Accept": "text/html,application/xhtml+xml", "User-Agent": "pytest-qa"})
    email, password = creds()
    r = s.get(f"{BASE_URL}/login", timeout=60)
    assert r.status_code == 200, r.status_code
    page = parse_page(r.text)
    VERSION["v"] = (page or {}).get("version", "")
    r = s.post(
        f"{BASE_URL}/login",
        data={"credential": email, "password": password},
        headers={"X-XSRF-TOKEN": xsrf(s), "Referer": f"{BASE_URL}/login"},
        allow_redirects=True,
        timeout=60,
    )
    if r.status_code != 200 or "/login" in r.url:
        pytest.fail(f"login failed {r.status_code} {r.url}")
    return s


def inertia_get(session, path):
    return session.get(
        f"{BASE_URL}{path}",
        headers={"X-Inertia": "true", "X-Inertia-Version": VERSION["v"], "Accept": "text/html, application/xhtml+xml"},
        timeout=30,
    )


def put_param(session, pid, payload):
    return session.put(
        f"{BASE_URL}/products/{pid}/parameters",
        json=payload,
        headers={
            "X-XSRF-TOKEN": xsrf(session),
            "X-Inertia": "true",
            "X-Inertia-Version": VERSION["v"],
            "Referer": f"{BASE_URL}/products/{pid}",
            "Content-Type": "application/json",
            "Accept": "text/html, application/xhtml+xml",
        },
        allow_redirects=False,
        timeout=30,
    )


PID = None


def product_id(session, alias="KRU"):
    global PID
    if PID:
        return PID
    r = inertia_get(session, "/products?per_page=100")
    assert r.status_code == 200, r.status_code
    data = r.json()["props"]
    rows = data.get("records") or data.get("rows") or {}
    rows = rows.get("data", rows) if isinstance(rows, dict) else rows
    assert rows, f"no product rows in props keys={list(data.keys())}"
    for row in rows:
        if str(row.get("alias")).upper() == alias:
            PID = row["id"]
            return PID
    PID = rows[0]["id"]
    return PID


# --- Halaman detail produk ---
class TestDetailPage:
    def test_show_page_props(self, client):
        pid = product_id(client)
        r = inertia_get(client, f"/products/{pid}")
        assert r.status_code == 200, r.text[:300]
        props = r.json()["props"]
        assert r.json()["component"] == "ProductDetail"
        assert props["product"]["id"] == pid
        assert len(props["methodOptions"]) >= 1
        assert len(props["installmentOptions"]) >= 1
        assert "_id" not in str(props["product"].keys())

    def test_show_invalid_id_404(self, client):
        r = inertia_get(client, "/products/999999")
        assert r.status_code == 404


# --- Simpan & persistensi parameter ---
class TestSaveParameter:
    def test_save_and_persist(self, client):
        pid = product_id(client)
        props = inertia_get(client, f"/products/{pid}").json()["props"]
        methods = [o["value"] for o in props["methodOptions"]][:3]
        insts = [o["value"] for o in props["installmentOptions"]][:2]
        payload = {
            "min_amount": 1000000, "max_amount": 500000000,
            "min_tenor": 3, "max_tenor": 60,
            "interest_rate": 1.5, "provision_rate": 1, "admin_rate": 0.5, "rc_threshold": 40,
            "allowed_method_ids": methods, "allowed_installment_ids": insts,
            "default_method_id": methods[0], "default_installment_id": insts[0],
            "collateral_required": True, "decree": "TEST_SK-001/DIR/2026", "note": "TEST catatan parameter",
        }
        r = put_param(client, pid, payload)
        assert r.status_code in (200, 302, 303), f"{r.status_code}: {r.text[:400]}"

        saved = inertia_get(client, f"/products/{pid}").json()["props"]["parameter"]
        assert saved is not None
        assert int(saved["min_amount"]) == 1000000
        assert int(saved["max_amount"]) == 500000000
        assert int(saved["min_tenor"]) == 3 and int(saved["max_tenor"]) == 60
        assert float(saved["interest_rate"]) == 1.5
        assert float(saved["provision_rate"]) == 1.0
        assert float(saved["admin_rate"]) == 0.5
        assert float(saved["rc_threshold"]) == 40.0
        assert sorted(map(int, saved["allowed_method_ids"])) == sorted(methods)
        assert sorted(map(int, saved["allowed_installment_ids"])) == sorted(insts)
        assert int(saved["default_method_id"]) == methods[0]
        assert saved["collateral_required"] is True
        assert saved["decree"] == "TEST_SK-001/DIR/2026"
        assert saved["note"] == "TEST catatan parameter"

    def test_resave_updates_same_row(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {
            "min_amount": 2000000, "max_amount": 400000000, "min_tenor": 6, "max_tenor": 48,
            "interest_rate": 2, "allowed_method_ids": [], "allowed_installment_ids": [],
            "collateral_required": False, "decree": "TEST_SK-002/DIR/2026",
        })
        assert r.status_code in (200, 302, 303), r.text[:300]
        saved = inertia_get(client, f"/products/{pid}").json()["props"]["parameter"]
        assert int(saved["min_amount"]) == 2000000
        assert saved["collateral_required"] is False
        assert saved["decree"] == "TEST_SK-002/DIR/2026"
        # allowed list cleared
        assert not saved["allowed_method_ids"]


# --- Validasi ---
class TestValidation:
    """Laravel/Inertia mengembalikan 422 (JSON) atau 303 redirect-back berisi errors di session."""

    def _errors(self, client, response):
        assert response.status_code in (303, 302, 422), f"unexpected {response.status_code}: {response.text[:300]}"
        if response.status_code == 422:
            body = response.json()
            return body.get("props", body).get("errors", {})
        location = response.headers.get("Location", "")
        path = location.replace(BASE_URL, "") or "/products"
        follow = inertia_get(client, path)
        assert follow.status_code == 200, follow.status_code
        return follow.json()["props"].get("errors", {})

    def test_max_amount_less_than_min(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {"min_amount": 5000000, "max_amount": 1000000})
        errs = self._errors(client, r)
        assert "max_amount" in errs, errs

    def test_max_tenor_less_than_min(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {"min_tenor": 24, "max_tenor": 6})
        errs = self._errors(client, r)
        assert "max_tenor" in errs, errs

    def test_percent_above_100(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {"interest_rate": 150, "provision_rate": 101, "admin_rate": 200, "rc_threshold": 120})
        errs = self._errors(client, r)
        for f in ("interest_rate", "provision_rate", "admin_rate", "rc_threshold"):
            assert f in errs, errs

    def test_default_method_not_in_allowed(self, client):
        pid = product_id(client)
        props = inertia_get(client, f"/products/{pid}").json()["props"]
        methods = [o["value"] for o in props["methodOptions"]]
        r = put_param(client, pid, {"allowed_method_ids": [methods[0]], "default_method_id": methods[-1]})
        errs = self._errors(client, r)
        assert "default_method_id" in errs
        msg = errs["default_method_id"]
        msg = msg if isinstance(msg, str) else msg[0]
        assert msg == "Metode bunga bawaan harus termasuk metode yang diizinkan."

    def test_default_installment_not_in_allowed(self, client):
        pid = product_id(client)
        props = inertia_get(client, f"/products/{pid}").json()["props"]
        insts = [o["value"] for o in props["installmentOptions"]]
        r = put_param(client, pid, {"allowed_installment_ids": [insts[0]], "default_installment_id": insts[-1]})
        errs = self._errors(client, r)
        assert "default_installment_id" in errs

    def test_nonexistent_method_id(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {"default_method_id": 999999})
        assert self._errors(client, r).get("default_method_id")

    def test_note_max_length(self, client):
        pid = product_id(client)
        r = put_param(client, pid, {"note": "T" * 1001})
        errs = self._errors(client, r)
        assert "note" in errs, errs


# --- Regresi halaman utama ---
class TestRegression:
    @pytest.mark.parametrize("path", [
        "/products", "/offices", "/institutions", "/installments", "/methods",
        "/committees", "/committees/2", "/users", "/users/create", "/menus", "/audit-trail",
    ])
    def test_pages_load(self, client, path):
        r = inertia_get(client, path)
        assert r.status_code == 200, f"{path} -> {r.status_code}"

    def test_audit_trail_has_product_module(self, client):
        r = inertia_get(client, "/audit-trail?per_page=50")
        assert r.status_code == 200
        assert "Data Produk" in r.text
        assert "parameter produk" in r.text.lower()


# --- Izin (403 tanpa products.manage) ---
class TestPermission:
    def test_guest_role_cannot_save(self, client):
        pid = product_id(client)
        import subprocess
        setup = subprocess.run(
            ["php", "artisan", "tinker", "--execute",
             "$u=\\App\\Models\\User::firstOrCreate(['email'=>'qa.param@test.local'],"
             "['name'=>'TEST QA Param','username'=>'qaparam','phone'=>'081299990001',"
             "'password'=>bcrypt('QA@4dm1n')]); $u->syncRoles(['Guest']); echo 'ok';"],
            cwd="/app/adminkit", capture_output=True, text=True, timeout=120)
        if "ok" not in setup.stdout:
            pytest.skip(f"cannot create guest user: {setup.stdout[-300:]} {setup.stderr[-300:]}")
        try:
            s = requests.Session()
            s.headers.update({"Accept": "text/html,application/xhtml+xml"})
            s.get(f"{BASE_URL}/login", timeout=60)
            lr = s.post(f"{BASE_URL}/login", data={"credential": "qa.param@test.local", "password": "QA@4dm1n"},
                        headers={"X-XSRF-TOKEN": xsrf(s), "Referer": f"{BASE_URL}/login"},
                        allow_redirects=True, timeout=60)
            assert "/login" not in lr.url, f"guest login failed: {lr.url}"
            r = put_param(s, pid, {"min_amount": 1})
            assert r.status_code == 403, f"expected 403 got {r.status_code}"
        finally:
            subprocess.run(["php", "artisan", "tinker", "--execute",
                            "\\App\\Models\\User::where('email','qa.param@test.local')->forceDelete(); echo 'done';"],
                           cwd="/app/adminkit", capture_output=True, text=True, timeout=120)
