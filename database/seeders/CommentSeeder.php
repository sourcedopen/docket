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
        $consumerTicket = Ticket::where('reference_number', 'TKT-2026-0003')->first();
        $bankingTicket = Ticket::where('reference_number', 'TKT-2026-0004')->first();
        $insuranceTicket = Ticket::where('reference_number', 'TKT-2026-0005')->first();
        $rtiReopenedTicket = Ticket::where('reference_number', 'TKT-2026-0007')->first();

        $comments = [
            // DND complaint comments
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

            // Consumer complaint comments
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

            // Banking complaint comments
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
                'body' => 'No update from SBI after promised timeline. Escalating to Banking Ombudsman directly.',
                'type' => CommentType::Escalation->value,
                'is_internal' => false,
                'created_at' => now()->subDays(2),
            ],

            // Insurance complaint — resolution trail
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

            // RTI reopened ticket
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
                'body' => 'Drafted first appeal pointing out that generic denial is not valid under RTI Act Section 7(8). Sending via speed post.',
                'type' => CommentType::Update->value,
                'is_internal' => false,
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($comments as $comment) {
            if ($comment['ticket_id']) {
                Comment::firstOrCreate(
                    [
                        'ticket_id' => $comment['ticket_id'],
                        'body' => $comment['body'],
                    ],
                    $comment
                );
            }
        }
    }
}
