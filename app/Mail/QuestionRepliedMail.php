<?php

namespace App\Mail;

use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionRepliedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $question;

    public function __construct(Question $question)
    {
        $this->question = $question;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Balasan Pertanyaan Anda - Literawaslu Bawaslu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.question_replied',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
