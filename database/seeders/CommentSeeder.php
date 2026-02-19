<?php

namespace Database\Seeders;

use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $dndTicket = Ticket::where('reference_number', 'TKT-2026-0002')->first();
        $dndAckTicket = Ticket::where('reference_number', 'TKT-2026-0009')->first();
        $consumerTicket = Ticket::where('reference_number', 'TKT-2026-0003')->first();
        $consumerChildTicket = Ticket::where('reference_number', 'TKT-2026-0008')->first();
        $bankingTicket = Ticket::where('reference_number', 'TKT-2026-0004')->first();
        $insuranceTicket = Ticket::where('reference_number', 'TKT-2026-0005')->first();
        $rtiReopenedTicket = Ticket::where('reference_number', 'TKT-2026-0007')->first();
        $civicTicket = Ticket::where('reference_number', 'TKT-2026-0011')->first();
        $overdueTicket = Ticket::where('reference_number', 'TKT-2025-0012')->first();
        $bankingLoanTicket = Ticket::where('reference_number', 'TKT-2026-0014')->first();

        $comments = [
            // --- DND complaint (submitted) comments ---
            [
                'ticket_id' => $dndTicket?->id,
                'user_id' => $user->id,
                'body' => 'Registered complaint with Jio customer care. Reference: JIO-CC-2026-11223. They said it will be resolved in 48 hours.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(3),
            ],
            [
                'ticket_id' => $dndTicket?->id,
                'user_id' => $user->id,
                'body' => 'No improvement after 48 hours. Filed formal complaint on TRAI DND portal.',
                'type' => CommentType::Escalation->value,
                'is_internal' => false,
                'created_at' => now()->subDays(1),
            ],
            [
                'ticket_id' => $dndTicket?->id,
                'user_id' => $user->id,
                'body' => 'Keeping track of call log screenshots as evidence for TRAI.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(1),
            ],

            // --- DND acknowledged ticket ---
            [
                'ticket_id' => $dndAckTicket?->id,
                'user_id' => $user->id,
                'body' => 'TRAI acknowledged the SMS spam complaint. Token: TRAI-SMS-2026-55123.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(5),
            ],

            // --- Consumer complaint (in progress) — long comment trail ---
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Sent legal notice to TechCorp via registered post. Tracking ID: EM123456789IN.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(18),
            ],
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Received call from TechCorp support manager. They acknowledged the defect but offered only repair, not refund. Rejected the offer.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(12),
            ],
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Need to gather purchase invoice and warranty card scan before next hearing.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(5),
            ],
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Filed complaint at District Consumer Forum. Case number: CC/2026/0342. Next hearing date: '.now()->addDays(20)->toDateString(),
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(2),
            ],
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Consulted Adv. Rajesh Sharma — he recommends asking for compensation in addition to refund.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(1),
            ],

            // --- Consumer child ticket ---
            [
                'ticket_id' => $consumerChildTicket?->id,
                'user_id' => $user->id,
                'body' => 'Requested inspection report from TechCorp authorized service center. Expected in 5 business days.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(8),
            ],

            // --- Banking complaint (escalated) — escalation trail ---
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'body' => 'Filed complaint on RBI CMS portal. Token number: CMS-2026-MH-00987.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(14),
            ],
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'body' => 'SBI branch manager called and confirmed investigation is underway. ETA for resolution: 10 working days.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(8),
            ],
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'body' => 'FIR copy filed at local police station. FIR No: 234/2026. Needed for bank claim.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(6),
            ],
            [
                'ticket_id' => $bankingTicket?->id,
                'user_id' => $user->id,
                'body' => 'No update from SBI after promised timeline. Escalating to Banking Ombudsman directly.',
                'type' => CommentType::Escalation->value,
                'is_internal' => false,
                'created_at' => now()->subDays(2),
            ],

            // --- Insurance complaint — full resolution trail ---
            [
                'ticket_id' => $insuranceTicket?->id,
                'user_id' => $user->id,
                'body' => 'Filed grievance on IRDAI IGMS portal. Grievance ID: IGMS-2026-LF-04521.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(40),
            ],
            [
                'ticket_id' => $insuranceTicket?->id,
                'user_id' => $user->id,
                'body' => 'National Insurance agreed to reprocess the claim after IRDAI intervention. They accepted the waiting period had lapsed.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(15),
            ],
            [
                'ticket_id' => $insuranceTicket?->id,
                'user_id' => $user->id,
                'body' => 'Claim amount of ₹1,50,000 credited to bank account. Closing this ticket.',
                'type' => CommentType::Resolution->value,
                'is_internal' => false,
                'created_at' => now()->subDays(5),
            ],

            // --- RTI reopened ticket ---
            [
                'ticket_id' => $rtiReopenedTicket?->id,
                'user_id' => $user->id,
                'body' => 'Original RTI response received. Most questions answered with "information not available" without citing specific Section 8 exemptions.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(20),
            ],
            [
                'ticket_id' => $rtiReopenedTicket?->id,
                'user_id' => $user->id,
                'body' => 'Researched Section 8(1) exemptions — none apply to recruitment data which is public information.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(10),
            ],
            [
                'ticket_id' => $rtiReopenedTicket?->id,
                'user_id' => $user->id,
                'body' => 'Drafted first appeal pointing out that generic denial is not valid under RTI Act Section 7(8). Sending via speed post.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(5),
            ],

            // --- Civic complaint ---
            [
                'ticket_id' => $civicTicket?->id,
                'user_id' => $user->id,
                'body' => 'Submitted complaint on PMC Sarathi portal with photos of the pothole and GPS location.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(4),
            ],
            [
                'ticket_id' => $civicTicket?->id,
                'user_id' => $user->id,
                'body' => 'Ward officer confirmed road repair team has been assigned. Work expected within a week.',
                'type' => CommentType::ResponseReceived->value,
                'is_internal' => false,
                'created_at' => now()->subDays(2),
            ],

            // --- Overdue consumer ticket ---
            [
                'ticket_id' => $overdueTicket?->id,
                'user_id' => $user->id,
                'body' => 'Called QuickShop support for the 4th time. They keep saying refund is "processing". Requesting supervisor callback.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(10),
            ],
            [
                'ticket_id' => $overdueTicket?->id,
                'user_id' => $user->id,
                'body' => 'If no refund by end of this week, will file on consumer helpline and National Consumer Commission portal.',
                'type' => CommentType::Escalation->value,
                'is_internal' => false,
                'created_at' => now()->subDays(3),
            ],

            // --- Banking loan ticket ---
            [
                'ticket_id' => $bankingLoanTicket?->id,
                'user_id' => $user->id,
                'body' => 'Attached sanction letter showing agreed processing fee of ₹5,000. Bank charged ₹15,000 without notification.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(1),
            ],

            // --- Soft-deleted comment — user removed an incorrect note ---
            [
                'ticket_id' => $consumerTicket?->id,
                'user_id' => $user->id,
                'body' => 'Wrong case number noted earlier. Correcting in next update.',
                'type' => CommentType::Note->value,
                'is_internal' => true,
                'created_at' => now()->subDays(15),
                'deleted' => true,
            ],
        ];

        foreach ($comments as $data) {
            if (! $data['ticket_id']) {
                continue;
            }

            $isDeleted = $data['deleted'] ?? false;
            unset($data['deleted']);

            $comment = Comment::withTrashed()->firstOrCreate(
                [
                    'ticket_id' => $data['ticket_id'],
                    'body' => $data['body'],
                ],
                $data
            );

            if ($isDeleted && ! $comment->trashed()) {
                $comment->delete();
            }
        }
    }
}
