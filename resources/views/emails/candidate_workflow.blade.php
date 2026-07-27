<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $subjectContent ?? 'Munchify Careers Notification' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style type="text/css">
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #F3F5F8;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            box-sizing: border-box;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            border-collapse: collapse !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        a {
            text-decoration: none;
        }

        /* Responsive Mobile Styles */
        @media screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                margin: 0 auto !important;
            }
            .content-padding {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
            .header-padding {
                padding: 28px 24px !important;
            }
            .mobile-headline {
                font-size: 22px !important;
                line-height: 30px !important;
            }
            .mobile-btn {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>
</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F3F5F8;">
    <center style="width: 100%; background-color: #F3F5F8; padding-top: 40px; padding-bottom: 50px;">
        
        <!-- Hidden Preheader -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            {{ Str::limit(strip_tags($bodyContent), 140) }}
        </div>

        <!-- Main Card Wrapper -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: auto; background-color: #FFFFFF; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(17, 19, 24, 0.05); border: 1px solid #E2E8F0;">
            
            <!-- Dark Premium Header -->
            <tr>
                <td style="background: #111318; padding: 36px 44px 32px 44px;" class="header-padding">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="left" valign="middle">
                                <a href="https://careers.munchify.co.ke" target="_blank">
                                    <img src="https://careers.munchify.co.ke/logo.png" alt="Munchify Careers" width="180" style="display: block; width: 180px; max-width: 180px; height: auto;">
                                </a>
                            </td>
                            <td align="right" valign="middle">
                                <span style="display: inline-block; background: rgba(255, 107, 0, 0.12); border: 1px solid rgba(255, 107, 0, 0.3); color: #FF6B00; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; font-weight: 700; padding: 6px 14px; border-radius: 99px; letter-spacing: 0.5px; text-transform: uppercase;">
                                    Recruitment
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Vibrant Gradient Divider Line -->
            <tr>
                <td style="background: linear-gradient(90deg, #FF6B00 0%, #FFD233 100%); height: 4px; font-size: 0; line-height: 0;">
                    &nbsp;
                </td>
            </tr>

            <!-- Card Body Area -->
            <tr>
                <td style="padding: 44px 44px 36px 44px;" class="content-padding">
                    
                    <!-- Candidate Greeting Header -->
                    <h1 class="mobile-headline" style="margin: 0 0 24px 0; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 26px; font-weight: 800; color: #0F172A; letter-spacing: -0.6px; line-height: 1.3;">
                        Hello {{ $candidateName ?: 'Candidate' }},
                    </h1>

                    <!-- Dynamic Body Content -->
                    <div style="font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.8; color: #334155; margin-bottom: 40px;">
                        {!! nl2br(e($bodyContent)) !!}
                    </div>

                    <!-- Highlighted Info Callout Box with Enhanced Spacing -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-left: 5px solid #FF6B00; border-radius: 16px; margin-bottom: 40px;">
                        <tr>
                            <td style="padding: 28px 32px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td valign="top" width="40" style="padding-right: 16px; font-size: 26px; line-height: 1;">
                                            📌
                                        </td>
                                        <td valign="top" style="font-family: 'Inter', sans-serif;">
                                            <h3 style="margin: 0 0 8px 0; color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; line-height: 1.4;">
                                                Need to check your application progress?
                                            </h3>
                                            <p style="margin: 0; font-size: 13.5px; line-height: 1.7; color: #475569;">
                                                You can track real-time stage updates, schedule interviews, and upload supplemental documents anytime via your candidate portal.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Primary Action CTA Button with Spacing -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 48px;">
                        <tr>
                            <td align="left">
                                <a href="{{ $actionUrl ?? 'https://careers.munchify.co.ke' }}" target="_blank" class="mobile-btn" style="background: linear-gradient(135deg, #FF6B00 0%, #E05D00 100%); color: #FFFFFF; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14.5px; font-weight: 700; text-decoration: none; padding: 18px 40px; border-radius: 99px; display: inline-block; box-shadow: 0 8px 20px rgba(255, 107, 0, 0.28); letter-spacing: 0.2px;">
                                    Track Your Application Progress &rarr;
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Professional Signature Block with Padding -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #E2E8F0; padding-top: 32px;">
                        <tr>
                            <td style="font-family: 'Inter', sans-serif; font-size: 14px; line-height: 1.8; color: #64748B;">
                                <span style="display: block; margin-bottom: 8px; color: #64748B;">Warm regards,</span>
                                <strong style="color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; display: block; margin-bottom: 4px;">
                                    Munchify Talent Acquisition Team
                                </strong>
                                <span style="font-size: 13px; color: #94A3B8; display: block; font-weight: 500;">
                                    Maseno University Campus Logistics & Operations
                                </span>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

            <!-- Elegant Footer -->
            <tr>
                <td style="background-color: #111318; padding: 40px 44px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.06);" class="header-padding">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="font-family: 'Inter', sans-serif; font-size: 12.5px; line-height: 1.9; color: #94A3B8;">
                                <strong style="color: #FFD233; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13.5px; font-weight: 700;">Munchify App Kenya</strong> &bull; Maseno University Main Campus<br>
                                Kisumu-Busia Highway, Maseno, Kenya<br>
                                <a href="mailto:careers@munchify.co.ke" style="color: #FF6B00; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 6px;">careers@munchify.co.ke</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding-top: 28px; border-top: 1px solid rgba(255, 255, 255, 0.08); margin-top: 24px;">
                                <p style="font-family: 'Inter', sans-serif; font-size: 11px; color: #64748B; margin: 0; line-height: 1.7;">
                                    &copy; {{ date('Y') }} Munchify App. All rights reserved.<br>
                                    This is an automated recruitment system message regarding your application.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>

    </center>
</body>
</html>
