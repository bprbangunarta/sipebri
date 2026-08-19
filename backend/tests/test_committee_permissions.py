"""Permission checks for /committees using a temporary Guest user.

Pre-req: `cd /app/adminkit && QA_MODE=create php artisan tinker --execute="require '/app/backend/tests/committee_permission_check.php';"`
Teardown: same command with QA_MODE=delete.
"""
import pytest

from test_committees import Client, errors_of, page_props, BASE_URL  # noqa: F401

GUEST_EMAIL = "qa_guest_committee@example.test"
GUEST_PASSWORD = "QaGuest@123"


@pytest.fixture(scope="module")
def guest():
    c = Client()
    c.get("/login")
    r = c.send("POST", "/login", {"credential": GUEST_EMAIL, "password": GUEST_PASSWORD}, referer="/login")
    if r.url.rstrip("/").endswith("/login"):
        pytest.fail(f"Guest login failed: {errors_of(r)}")
    return c


def test_guest_cannot_view_committees(guest):
    r = guest.get("/committees", allow_redirects=False)
    assert r.status_code == 403, r.status_code


def test_guest_cannot_store_path(guest):
    r = guest.send("POST", "/committees", {"mechanism": "plafon", "is_active": 1},
                   referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code


def test_guest_cannot_update_or_delete_path(guest):
    r = guest.send("PUT", "/committees/1", {"mechanism": "plafon"}, referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code
    r = guest.send("DELETE", "/committees/1", referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code


def test_guest_cannot_manage_tiers(guest):
    r = guest.send("POST", "/committees/1/tiers", {"role": "Staff Analis"}, referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code
    r = guest.send("PUT", "/committees/1/tiers/1/move/up", referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code
    r = guest.send("DELETE", "/committees/1/tiers/1", referer="/", allow_redirects=False)
    assert r.status_code == 403, r.status_code
