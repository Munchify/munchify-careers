<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $subjectContent ?? 'Munchify Careers Notification' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Reset styles */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #F4F6FA;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
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

        /* Mobile Responsive */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
            }
            .fluid-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .mobile-headline {
                font-size: 22px !important;
                line-height: 30px !important;
            }
            .mobile-button {
                width: 100% !important;
                text-align: center !important;
                display: block !important;
            }
        }
    </style>
</head>
<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F4F6FA;">
    <center style="width: 100%; background-color: #F4F6FA;">
        
        <!-- Preheader text -->
        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            {{ Str::limit(strip_tags($bodyContent), 140) }}
        </div>

        <!-- Email Main Container -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: auto; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; margin-top: 40px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(17, 19, 24, 0.06); border: 1px solid #EAEFF5;">
            
            <!-- Top Gradient Header Banner -->
            <tr>
                <td style="background: linear-gradient(135deg, #111318 0%, #1D222E 100%); padding: 36px 40px; text-align: left;" class="fluid-padding">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="left">
                                <a href="https://careers.munchify.co.ke" target="_blank">
                                    <img src="https://careers.munchify.co.ke/logo.png" alt="Munchify Careers" width="180" style="display: block; width: 180px; max-width: 180px; height: auto; font-family: 'Sora', sans-serif; color: #FFFFFF; font-size: 20px; font-weight: bold;">
                                </a>
                            </td>
                            <td align="right" style="font-family: 'Sora', sans-serif; font-size: 11px; color: #FF6B00; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                Recruitment Portal
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Orange Brand Accent Bar -->
            <tr>
                <td style="background: linear-gradient(90deg, #FF6B00 0%, #FFD233 100%); height: 4px; font-size: 0; line-height: 0;">
                    &nbsp;
                </td>
            </tr>

            <!-- Main Body Content -->
            <tr>
                <td style="padding: 40px 40px 30px 40px;" class="fluid-padding">
                    
                    <!-- Candidate Greeting -->
                    <h1 class="mobile-headline" style="margin: 0 0 20px 0; font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 800; color: #111318; letter-spacing: -0.5px;">
                        Hello {{ $candidateName ?: 'Candidate' }},
                    </h1>

                    <!-- Dynamic Body Content Block -->
                    <div style="font-family: 'Inter', sans-serif; font-size: 15px; line-height: 1.7; color: #374151; margin-bottom: 30px;">
                        {!! nl2br(e($bodyContent)) !!}
                    </div>

                    <!-- Informational Callout Card -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAF7; border: 1px solid #E2E8F0; border-left: 4px solid #FF6B00; border-radius: 12px; margin-bottom: 32px;">
                        <tr>
                            <td style="padding: 20px 24px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td valign="top" width="28" style="padding-right: 12px;">
                                            <span style="font-size: 20px; line-height: 1;">📌</span>
                                        </td>
                                        <td style="font-family: 'Inter', sans-serif; font-size: 13px; line-height: 1.6; color: #475569;">
                                            <strong style="color: #0F172A; display: block; margin-bottom: 2px;">Need to check your application progress?</strong>
                                            You can track real-time stage updates, schedule interviews, and upload supplemental documents anytime via your candidate portal.
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Primary Call to Action Button -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 35px;">
                        <tr>
                            <td align="left">
                                <a href="{{ $actionUrl ?? 'https://careers.munchify.co.ke' }}" target="_blank" class="mobile-button" style="background: linear-gradient(135deg, #FF6B00 0%, #E05D00 100%); color: #FFFFFF; font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 99px; display: inline-block; box-shadow: 0 4px 14px rgba(255, 107, 0, 0.25); text-align: center;">
                                    Track Your Application Progress &rarr;
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing Signature Block -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #F1F5F9; padding-top: 24px;">
                        <tr>
                            <td style="font-family: 'Inter', sans-serif; font-size: 14px; line-height: 1.6; color: #64748B;">
                                Warm regards,<br>
                                <strong style="color: #111318; font-family: 'Sora', sans-serif; font-size: 15px;">Munchify Talent Acquisition Team</strong><br>
                                <span style="font-size: 12px; color: #94A3B8;">Maseno University Campus Logistics & Operations</span>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

            <!-- Footer Section -->
            <tr>
                <td style="background-color: #111318; padding: 32px 40px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.05);" class="fluid-padding">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="font-family: 'Inter', sans-serif; font-size: 12px; line-height: 1.8; color: #94A3B8;">
                                <strong style="color: #FFD233; font-family: 'Sora', sans-serif;">Munchify App Kenya</strong> &bull; Maseno University Main Campus<br>
                                Kisumu-Busia Highway, Maseno, Kenya<br>
                                <a href="mailto:careers@munchify.co.ke" style="color: #FF6B00; text-decoration: none; font-weight: 600;">careers@munchify.co.ke</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding-top: 20px;">
                                <p style="font-family: 'Inter', sans-serif; font-size: 11px; color: #64748B; margin: 0;">
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
