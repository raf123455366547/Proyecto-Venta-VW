<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Sobreescribe la notificación de restablecimiento de contraseña en español.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new class($token) extends ResetPassword {
            public function toMail($notifiable)
            {
                $url = url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));

                return (new MailMessage)
                    ->subject('Restablecer Contraseña - ANCGVW')
                    ->greeting('¡Hola!')
                    ->line('Recibiste este correo porque solicitaste un restablecimiento de contraseña para tu cuenta.')
                    ->action('Restablecer Contraseña', $url)
                    ->line('Este enlace para restablecer la contraseña caducará en 60 minutos.')
                    ->line('Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción.')
                    ->salutation('Saludos, ANCGVW');
            }
        });
    }
}