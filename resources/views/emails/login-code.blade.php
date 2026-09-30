<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body style="margin:0;padding:24px;background:#f5f6f8;font-family:Arial,Helvetica,sans-serif;color:#1f2430;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table role="presentation" width="420" cellpadding="0" cellspacing="0" style="max-width:420px;background:#ffffff;border:1px solid #e6e8ee;border-radius:8px;">
                        <tr>
                            <td style="padding:20px 24px 8px;font-size:15px;font-weight:bold;">{{ config('app.name') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:0 24px;font-size:14px;line-height:1.5;">
                                Use this code to verify it is you:
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding:16px 24px;">
                                <span style="display:inline-block;padding:10px 18px;background:#f5f6f8;border-radius:6px;font-size:26px;letter-spacing:6px;font-weight:bold;">{{ $code }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 24px 20px;font-size:12px;line-height:1.5;color:#6b7280;">
                                The code is valid for {{ $minutes }} minutes. If you did not try to sign in or change your security settings, ignore this email and tell your administrator.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
