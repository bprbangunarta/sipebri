"""Backend tests for AdminKit / SIPEBRI module: Komite Kredit (/committees).

Auth: Laravel session guard (credential/password). Inertia props read from the
`data-page` script tag. Only self-created paths are deleted (17 seeded paths kept).
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
MARK = "QATEST"  # condition marker used for all self-created paths


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

    def send(self, method, path, data=None, referer="/", allow_redirects=True):
        headers = {
            "X-XSRF-TOKEN": self.token(),
            "Referer": f"{BASE_URL}{referer}",
            "Accept": "text/html, application/xhtml+xml",
        }
        return self.s.request(
            method, f"{BASE_URL}{path}", data=data, headers=headers,
            timeout=30, allow_redirects=allow_redirects,
        )


def errors_of(resp):
    props = page_props(resp) or {}
    return props.get("props", {}).get("errors", {}) or {}


@pytest.fixture(scope="session")
def client():
    c = Client()
    c.get("/login")
    r = c.send("POST", "/login", {"credential": EMAIL, "password": PASSWORD}, referer="/login")
    if r.status_code != 200 or r.url.rstrip("/").endswith("/login"):
        pytest.fail(f"Login failed: {r.status_code} url={r.url} errors={errors_of(r)}")
    return c


@pytest.fixture(scope="session")
def index_props(client):
    r = client.get("/committees")
    assert r.status_code == 200, r.text[:300]
    return page_props(r)["props"]


@pytest.fixture(scope="session")
def created_ids():
    return []


@pytest.fixture(scope="session", autouse=True)
def cleanup(client, created_ids):
    yield
    for pid in created_ids:
        client.send("DELETE", f"/committees/{pid}", referer="/committees")
    # safety net: remove any leftover path carrying the QATEST marker
    props = page_props(client.get("/committees"))["props"]
    for row in props["paths"]:
        if MARK in str(row.get("condition") or ""):
            client.send("DELETE", f"/committees/{row['id']}", referer="/committees")


def free_product(client):
    """A product id that has no committee path yet (None if all taken)."""
    props = page_props(client.get("/committees"))["props"]
    used = {row["product_id"] for row in props["paths"]}
    for opt in props["productOptions"]:
        if opt["value"] and int(opt["value"]) not in used:
            return opt["value"]
    return None


def create_path(client, created_ids, **data):
    payload = {"mechanism": "plafon", "is_active": 1, **data}
    r = client.send("POST", "/committees", payload, referer="/committees")
    m = re.search(r"/committees/(\d+)", r.url)
    if m:
        created_ids.append(int(m.group(1)))
    return r


# ── Index / seeded data ──────────────────────────────────────────────────
def test_index_loads_with_seeded_paths(index_props):
    paths = index_props["paths"]
    assert len(paths) >= 17, f"expected >=17 seeded paths, got {len(paths)}"
    row = paths[0]
    for key in ("product_label", "condition_label", "mechanism_label", "tiers_count", "status_label"):
        assert key in row
    assert not any("_id" == k for k in row)


def test_seeded_reloan_all_products_row_exists(index_props):
    match = [r for r in index_props["paths"] if r["product_id"] is None and r["condition"] == "RELOAN"]
    assert match, "Semua Produk / RELOAN path missing"
    assert match[0]["product_label"] == "Semua Produk"


def test_total_seeded_tiers_is_111(index_props):
    seeded = [r for r in index_props["paths"] if MARK not in str(r.get("condition") or "")]
    total = sum(r["tiers_count"] for r in seeded)
    assert total == 111, f"expected 111 seeded tiers, got {total}"


def test_product_and_path_options_present(index_props):
    assert index_props["productOptions"][0] == {"value": "", "label": "Semua Produk"}
    assert len(index_props["productOptions"]) > 5
    assert len(index_props["pathOptions"]) >= 17


# ── Detail (show) ────────────────────────────────────────────────────────
def test_kru_plafon_path_detail(client, index_props):
    kru = [r for r in index_props["paths"]
           if r["product_label"].startswith("KRU") and not r["condition"]]
    assert kru, "KRU normal path not found"
    r = client.get(f"/committees/{kru[0]['id']}")
    assert r.status_code == 200
    path = page_props(r)["props"]["path"]
    assert path["mechanism"] == "plafon"
    assert len(path["tiers"]) == 7, f"KRU should have 7 tiers, got {len(path['tiers'])}"
    kasi = [t for t in path["tiers"] if t["role"] == "Kasi Analis"]
    assert kasi, "Kasi Analis tier missing"
    assert kasi[0]["min_amount"] == 1000
    assert kasi[0]["max_amount"] == 35000000
    assert path["tiers"][-1]["max_amount"] is None  # Komite III: 300.000.001 ke atas
    assert [t["sort"] for t in path["tiers"]] == sorted(t["sort"] for t in path["tiers"])


def test_kup_hierarki_path_detail(client, index_props):
    kup = [r for r in index_props["paths"] if r["product_label"].startswith("KUP")]
    assert kup, "KUP path not found"
    r = client.get(f"/committees/{kup[0]['id']}")
    path = page_props(r)["props"]["path"]
    assert path["mechanism"] == "hierarki"
    assert len(path["tiers"]) == 5, f"KUP should have 5 tiers, got {len(path['tiers'])}"
    assert all(t["min_amount"] is None and t["max_amount"] is None for t in path["tiers"])


def test_show_provides_role_options(client, index_props):
    r = client.get(f"/committees/{index_props['paths'][0]['id']}")
    roles = page_props(r)["props"]["roleOptions"]
    assert len(roles) >= 10 and all("value" in o and "label" in o for o in roles)


def test_show_unknown_path_returns_404(client):
    assert client.get("/committees/99999").status_code == 404


# ── Create path ──────────────────────────────────────────────────────────
def test_create_path_with_copy_tiers(client, index_props, created_ids):
    source = max(index_props["paths"], key=lambda r: r["tiers_count"])
    pid = free_product(client)
    data = {"condition": f"{MARK}A", "mechanism": "plafon", "copy_from": source["id"]}
    if pid:
        data["product_id"] = pid
    r = create_path(client, created_ids, **data)
    assert r.status_code == 200, r.text[:300]
    assert re.search(r"/committees/\d+$", r.url), f"not redirected to detail: {r.url}"
    path = page_props(r)["props"]["path"]
    assert len(path["tiers"]) == source["tiers_count"], "copied tier count mismatch"
    assert path["condition"] == f"{MARK}A"


def test_condition_is_uppercased(client, created_ids):
    r = create_path(client, created_ids, condition=f"{MARK.lower()}b", mechanism="hierarki")
    assert r.status_code == 200
    path = page_props(r)["props"]["path"]
    assert path["condition"] == f"{MARK}B", path["condition"]


def test_duplicate_product_condition_rejected(client, created_ids, index_props):
    seeded = [r for r in index_props["paths"] if r["condition"]][0]
    data = {"condition": seeded["condition"], "mechanism": "plafon"}
    if seeded["product_id"]:
        data["product_id"] = str(seeded["product_id"])
    r = client.send("POST", "/committees", {"is_active": 1, **data}, referer="/committees")
    errs = errors_of(r)
    assert errs.get("condition") == "Jalur untuk produk dan kondisi tersebut sudah ada.", errs


def test_duplicate_normal_condition_rejected(client, index_props, created_ids):
    """Same product with empty condition (Normal) twice should be rejected too."""
    seeded = [r for r in index_props["paths"] if r["product_id"] and not r["condition"]][0]
    r = client.send(
        "POST", "/committees",
        {"product_id": str(seeded["product_id"]), "condition": "", "mechanism": "plafon", "is_active": 1},
        referer="/committees",
    )
    m = re.search(r"/committees/(\d+)$", r.url)
    if m:
        created_ids.append(int(m.group(1)))
    assert errors_of(r).get("condition"), "duplicate Normal path was accepted (no validation error)"


def test_invalid_mechanism_rejected(client):
    r = client.send("POST", "/committees", {"mechanism": "bogus", "is_active": 1}, referer="/committees")
    assert errors_of(r).get("mechanism"), errors_of(r)


def test_invalid_product_rejected(client):
    r = client.send(
        "POST", "/committees",
        {"product_id": "999999", "mechanism": "plafon", "is_active": 1}, referer="/committees",
    )
    assert errors_of(r).get("product_id"), errors_of(r)


# ── Update path ──────────────────────────────────────────────────────────
def test_update_path_mechanism_and_status(client, created_ids):
    r = create_path(client, created_ids, condition=f"{MARK}C", mechanism="plafon")
    pid = int(re.search(r"/committees/(\d+)", r.url).group(1))

    upd = client.send(
        "PUT", f"/committees/{pid}",
        {"condition": f"{MARK}C", "mechanism": "hierarki", "is_active": 0, "note": "diubah QA"},
        referer=f"/committees/{pid}",
    )
    assert upd.status_code == 200, upd.text[:300]
    path = page_props(client.get(f"/committees/{pid}"))["props"]["path"]
    assert path["mechanism"] == "hierarki"
    assert path["is_active"] is False
    assert path["status_label"] == "Nonaktif"
    assert path["note"] == "diubah QA"


# ── Tiers CRUD ───────────────────────────────────────────────────────────
@pytest.fixture(scope="session")
def tier_path(client, created_ids):
    r = create_path(client, created_ids, condition=f"{MARK}T", mechanism="plafon")
    return int(re.search(r"/committees/(\d+)", r.url).group(1))


@pytest.fixture(scope="session")
def a_role(client, index_props):
    r = client.get(f"/committees/{index_props['paths'][0]['id']}")
    return page_props(r)["props"]["roleOptions"][0]["value"]


def tiers_of(client, pid):
    return page_props(client.get(f"/committees/{pid}"))["props"]["path"]["tiers"]


def test_tier_create_update_reorder_delete(client, tier_path, a_role):
    ref = f"/committees/{tier_path}"

    # create two tiers
    for label, lo, hi in (("QA Jenjang 1", 1000, 5000000), ("QA Jenjang 2", 5000001, 20000000)):
        r = client.send("POST", f"{ref}/tiers", {
            "label": label, "role": a_role, "min_amount": lo, "max_amount": hi,
            "can_escalate": 1, "can_approve": 1, "can_cancel": 0, "can_reject": 0,
        }, referer=ref)
        assert r.status_code == 200, r.text[:300]
        assert not errors_of(r), errors_of(r)

    tiers = tiers_of(client, tier_path)
    assert len(tiers) == 2
    assert tiers[0]["label"] == "QA Jenjang 1"
    assert tiers[0]["min_amount"] == 1000 and tiers[0]["max_amount"] == 5000000
    assert tiers[0]["can_escalate"] is True and tiers[0]["can_cancel"] is False
    assert [t["sort"] for t in tiers] == [1, 2]

    # update first tier
    t1 = tiers[0]
    r = client.send("PUT", f"{ref}/tiers/{t1['id']}", {
        "label": "QA Jenjang 1B", "role": a_role, "min_amount": 2000, "max_amount": 6000000,
        "can_escalate": 0, "can_approve": 1, "can_cancel": 1, "can_reject": 1,
    }, referer=ref)
    assert r.status_code == 200 and not errors_of(r)
    t1b = tiers_of(client, tier_path)[0]
    assert t1b["label"] == "QA Jenjang 1B" and t1b["min_amount"] == 2000
    assert t1b["can_cancel"] is True and t1b["can_escalate"] is False

    # move second tier up -> order swaps and persists
    second = tiers_of(client, tier_path)[1]
    r = client.send("PUT", f"{ref}/tiers/{second['id']}/move/up", referer=ref)
    assert r.status_code == 200, r.text[:300]
    after = tiers_of(client, tier_path)
    assert after[0]["id"] == second["id"], "move up did not swap"
    assert [t["sort"] for t in after] == [1, 2]

    # move first tier up again -> no-op
    r = client.send("PUT", f"{ref}/tiers/{after[0]['id']}/move/up", referer=ref)
    assert r.status_code == 200
    assert tiers_of(client, tier_path)[0]["id"] == second["id"]

    # invalid direction
    bad = client.send("PUT", f"{ref}/tiers/{after[0]['id']}/move/sideways", referer=ref,
                      allow_redirects=False)
    assert bad.status_code in (404, 405), bad.status_code

    # delete both tiers
    for t in tiers_of(client, tier_path):
        r = client.send("DELETE", f"{ref}/tiers/{t['id']}", referer=ref)
        assert r.status_code == 200
    assert tiers_of(client, tier_path) == []


def test_tier_validation_max_less_than_min(client, tier_path, a_role):
    ref = f"/committees/{tier_path}"
    r = client.send("POST", f"{ref}/tiers", {
        "label": "QA Bad", "role": a_role, "min_amount": 5000000, "max_amount": 1000,
    }, referer=ref)
    assert errors_of(r).get("max_amount") == \
        "Plafon maksimal tidak boleh lebih kecil dari plafon minimal.", errors_of(r)


def test_tier_validation_role_required(client, tier_path):
    ref = f"/committees/{tier_path}"
    r = client.send("POST", f"{ref}/tiers", {"label": "QA NoRole"}, referer=ref)
    assert errors_of(r).get("role"), errors_of(r)


def test_tier_validation_unknown_role(client, tier_path):
    ref = f"/committees/{tier_path}"
    r = client.send("POST", f"{ref}/tiers", {"label": "QA X", "role": "Tidak Ada"}, referer=ref)
    assert errors_of(r).get("role"), errors_of(r)


def test_tier_from_other_path_cannot_be_deleted(client, tier_path, created_ids, a_role):
    """Scoped binding check using only QA-created paths (never seeded data)."""
    r = create_path(client, created_ids, condition=f"{MARK}S", mechanism="plafon")
    other = int(re.search(r"/committees/(\d+)", r.url).group(1))
    ref_other = f"/committees/{other}"
    client.send("POST", f"{ref_other}/tiers", {"label": "QA Foreign", "role": a_role}, referer=ref_other)
    foreign = tiers_of(client, other)[0]

    resp = client.send("DELETE", f"/committees/{tier_path}/tiers/{foreign['id']}",
                       referer=f"/committees/{tier_path}", allow_redirects=False)
    still_there = any(t["id"] == foreign["id"] for t in tiers_of(client, other))
    assert still_there, "tier of another path was deleted via a mismatched path route (missing scopeBindings)"
    assert resp.status_code == 404, f"expected 404 for mismatched path/tier, got {resp.status_code}"


# ── Delete path cascade ──────────────────────────────────────────────────
def test_delete_path_cascades_tiers(client, created_ids, index_props):
    source = max(index_props["paths"], key=lambda r: r["tiers_count"])
    r = create_path(client, created_ids, condition=f"{MARK}D", mechanism="plafon", copy_from=source["id"])
    pid = int(re.search(r"/committees/(\d+)", r.url).group(1))
    tiers = tiers_of(client, pid)
    assert len(tiers) > 0

    d = client.send("DELETE", f"/committees/{pid}", referer=f"/committees/{pid}")
    assert d.status_code == 200
    if pid in created_ids:
        created_ids.remove(pid)
    assert client.get(f"/committees/{pid}").status_code == 404
    rows = page_props(client.get("/committees"))["props"]["paths"]
    assert pid not in [x["id"] for x in rows]
    # tiers gone (cascade): recreating same path yields zero tiers
    r2 = create_path(client, created_ids, condition=f"{MARK}D", mechanism="plafon")
    assert tiers_of(client, int(re.search(r"/committees/(\d+)", r2.url).group(1))) == []


# ── Permissions matrix + audit trail ─────────────────────────────────────
def test_permission_matrix_has_committees_entity(client):
    r = client.get("/roles/1")
    assert r.status_code == 200
    body = json.dumps(page_props(r)["props"])
    assert "committees.view" in body and "committees.manage" in body
    assert "Komite Kredit" in body, "entity label 'Komite Kredit' missing in role matrix"


def test_audit_trail_records_committee_module(client):
    r = client.get("/audit-trail?search=jalur+komite")
    assert r.status_code == 200
    body = json.dumps(page_props(r)["props"])
    assert "Komite Kredit" in body, "no Komite Kredit activity found in audit trail"


# ── Regression: other pages still load ───────────────────────────────────
@pytest.mark.parametrize("path", [
    "/institutions", "/products", "/installments", "/methods",
    "/users", "/users/create", "/menus", "/roles", "/permissions",
    "/appearance", "/object-storage", "/audit-trail", "/profile",
])
def test_other_pages_load(client, path):
    r = client.get(path)
    assert r.status_code == 200, f"{path} -> {r.status_code}"
