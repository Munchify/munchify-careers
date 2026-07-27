<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'event_key' => 'application_received',
                'name' => 'Application Received Confirmation',
                'email_subject' => 'Application Received: {job_title} ({app_number})',
                'email_body' => "Thank you for applying for the position of {job_title} at Munchify. Your application number is {app_number}.\n\nYou can track the real-time status of your application anytime at:\n{status_url}\n\nOur hiring team will review your application and update you on the next steps.",
                'sms_body' => 'Thank you for applying for {job_title} at Munchify. App #: {app_number}. Track status: {status_url}',
                'whatsapp_body' => "Hello *{name}*! 👋\n\nThank you for applying for the position of *{job_title}* at Munchify. Your application number is *{app_number}*.\n\nYou can track the progress of your application in real-time here:\n{status_url}\n\nWe will review your profile and get back to you soon!\n\nBest regards,\n*Munchify Recruitment Team*",
                'whatsapp_template_name' => 'app_received_v1',
                'description' => 'Sent automatically to candidate upon submitting a new job application.',
            ],
            [
                'event_key' => 'stage_moved',
                'name' => 'Stage Moved',
                'email_subject' => 'Application Update: {job_title}',
                'email_body' => "Great news! Your application for {job_title} has progressed to the {stage_name} stage.\n\nYou can view your updated application status here:\n{status_url}\n\nOur team will be in touch if any additional steps or interview bookings are needed.",
                'sms_body' => 'Hi {name}, your application for {job_title} has progressed to the {stage_name} stage. Track progress at {status_url} - Munchify.',
                'whatsapp_body' => "Hi *{name}*! 🎉\n\nGood news! Your application for *{job_title}* has progressed to the *{stage_name}* stage.\n\nKeep track of your application status here:\n{status_url}\n\nOur team will contact you shortly if further details or scheduling is required.\n\nBest of luck,\n*Munchify Recruitment Team*",
                'description' => 'Sent when a candidate is moved to a new stage (if auto-notification is enabled).',
            ],
            [
                'event_key' => 'interview_scheduled',
                'name' => 'Interview Scheduled',
                'email_subject' => 'Interview Scheduled for {job_title}',
                'email_body' => "Your interview for the {job_title} role has been scheduled.\n\nDate & Time: {scheduled_at}\nFormat/Type: {type}\nDetails/Link: {details}\n\nYou can track and confirm your schedule here:\n{status_url}\n\nPlease prepare to join on time.",
                'sms_body' => 'Hi {name}, your interview for {job_title} is scheduled on {scheduled_at} ({type}). Details: {status_url} - Munchify.',
                'whatsapp_body' => "Hello *{name}*! 📅\n\nYour interview for the *{job_title}* role has been scheduled.\n\n🗓️ *Date & Time:* {scheduled_at}\n💻 *Type:* {type}\n📍 *Details/Link:* {details}\n\nCheck your full schedule details and track status here:\n{status_url}\n\nPlease let us know if you have any questions.\n\nWarm regards,\n*Munchify Recruitment Team*",
                'description' => 'Sent when an interview is scheduled.',
            ],
            [
                'event_key' => 'interview_reminder',
                'name' => 'Interview Reminder',
                'email_subject' => 'Reminder: Upcoming Interview for {job_title}',
                'email_body' => "This is a reminder for your upcoming interview for {job_title}.\n\nScheduled Time: {scheduled_at}\nFormat/Type: {type}\nDetails/Link: {details}\n\nTrack status & details:\n{status_url}\n\nWe look forward to speaking with you!",
                'sms_body' => 'Hi {name}, reminder for your interview for {job_title} tomorrow at {scheduled_at}. Details: {status_url} - Munchify.',
                'whatsapp_body' => "Hi *{name}*! 🔔\n\nThis is a quick reminder that you have an upcoming interview for the *{job_title}* role tomorrow.\n\n🗓️ *Date & Time:* {scheduled_at}\n💻 *Type:* {type}\n\nPlease ensure you are online/ready at least 5 minutes before the time.\n\nView details here:\n{status_url}\n\nSee you then!\n*Munchify Recruitment Team*",
                'description' => 'Sent 24 hours before a scheduled interview (artisan reminder job).',
            ],
            [
                'event_key' => 'hired',
                'name' => 'Offer / Hired',
                'email_subject' => 'Congratulations! Job Offer for {job_title}',
                'email_body' => "We are thrilled to offer you the position of {job_title} at Munchify!\n\nAfter reviewing your application and interviews, our team is confident you will be a great fit for Munchify.\n\nPlease check your status page for next steps:\n{status_url}\n\nWelcome to the team!",
                'sms_body' => 'Congratulations {name}! You have been offered the role of {job_title} at Munchify. Check details at {status_url}.',
                'whatsapp_body' => "Congratulations *{name}*! 🎉🥳\n\nWe are absolutely thrilled to offer you the position of *{job_title}* at Munchify!\n\nThe hiring team has reviewed your interviews and we believe you will be a fantastic addition to the Munchify family.\n\nYou can view the next steps and details on your status tracker:\n{status_url}\n\nWelcome aboard!\n\nBest regards,\n*Munchify Executive Team*",
                'description' => 'Sent when a candidate is moved to the Hired stage.',
            ],
            [
                'event_key' => 'rejected',
                'name' => 'Application Rejected',
                'email_subject' => 'Update regarding your application for {job_title}',
                'email_body' => "Thank you for applying and taking the time to interview for {job_title} at Munchify.\n\nAfter careful consideration, we regret to inform you that we will not be moving forward with your application at this time. We had many qualified candidates and difficult decisions to make.\n\nWe will keep your resume on file for future opportunities. We wish you success in your career search.",
                'sms_body' => 'Hi {name}, thank you for your interest in {job_title} at Munchify. We regret to inform you that we will not be moving forward. Munchify.',
                'whatsapp_body' => "Dear *{name}*,\n\nThank you for taking the time to apply and interview for the *{job_title}* position at Munchify.\n\nAfter careful consideration, we regret to inform you that we will not be moving forward with your application at this time. The competition was strong, and we had to make some very difficult decisions.\n\nWe will keep your CV on file for future openings that match your skills. We wish you the absolute best in your career journey.\n\nSincerely,\n*Munchify HR Team*",
                'description' => 'Sent when a candidate is moved to the Rejected stage.',
            ],
        ];

        foreach ($templates as $tmpl) {
            NotificationTemplate::updateOrCreate(
                ['event_key' => $tmpl['event_key']],
                $tmpl
            );
        }
    }
}
