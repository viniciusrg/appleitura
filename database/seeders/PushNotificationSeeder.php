<?php

namespace Database\Seeders;

use App\Models\NotificationMessage;
use Illuminate\Database\Seeder;

class PushNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            '🏆 Você está a um passo de completar mais uma jornada de autoconhecimento! Volte agora e veja como você pode evoluir ainda mais. 🌟',
            '📚✨ E aí, curioso? Tem novos livros poderosos esperando por você! Clique e descubra como evoluir ainda mais hoje! 🌟',
            '🚀 Ei, campeão! Que tal dar mais um passo rumo à sua melhor versão? Sua próxima leitura está te esperando. Vamos lá? 💪',
            '🤗 Saudades de você! Temos novos títulos que vão te ajudar a crescer ainda mais. Vem se juntar a gente novamente! 🌱',
            '⏰ Ei, não perca tempo! A próxima grande sacada para seu crescimento está te esperando. Corre aqui e confira agora! 🚀',
            '🔥 Pronto para o próximo desafio? Vamos ver até onde você pode ir hoje. Acesse o app e continue sua jornada! 💪',
            '🔍 Tem um novo segredo do desenvolvimento pessoal esperando por você! Venha descobrir algo incrível hoje mesmo! ✨',
            '🌟 Você está arrasando! Continue seu progresso e veja o quanto já evoluiu. Volte para o app e continue brilhando! 🌟'
        ];

        foreach ($notifications as $notification) {
            NotificationMessage::create([
                'message' => $notification
            ]);
        }
    }
}
