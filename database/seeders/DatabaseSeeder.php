<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Avis;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $photos = [
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1560185007-cde436f6a4d0?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1449844908441-8829872d2607?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80&fit=crop',
            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=1200&q=80&fit=crop',
        ];

        User::factory()->create([
            'name' => 'Admin Mini-Rb',
            'email' => 'admin@mini-rb.test',
            'role' => 'admin',
        ]);

        $demo = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $hosts = User::factory()->count(6)->create(['role' => 'user']);
        $hosts->push($demo);
        $travelers = User::factory()->count(10)->create(['role' => 'user']);

        $listings = [
            ['Riad traditionnel avec patio', 'Marrakech', 900, 4],
            ['Appartement moderne à Guéliz', 'Marrakech', 550, 2],
            ['Villa avec piscine à la Palmeraie', 'Marrakech', 2400, 5],
            ['Riad de charme dans la médina', 'Fès', 700, 3],
            ['Appartement lumineux centre-ville', 'Fès', 450, 2],
            ['Appartement vue océan', 'Casablanca', 800, 2],
            ['Studio cosy à Maârif', 'Casablanca', 400, 1],
            ['Penthouse moderne à Anfa', 'Casablanca', 1600, 3],
            ['Appartement calme à Agdal', 'Rabat', 650, 2],
            ['Maison traditionnelle près du Kasbah', 'Rabat', 750, 3],
            ['Appartement vue port', 'Tanger', 700, 2],
            ['Villa sur les hauteurs', 'Tanger', 1800, 4],
            ['Maison bleue dans la médina', 'Chefchaouen', 500, 2],
            ['Riad paisible aux montagnes', 'Chefchaouen', 600, 3],
            ['Appartement front de mer', 'Agadir', 750, 2],
            ['Villa avec jardin', 'Agadir', 1500, 4],
            ['Maison d\'hôtes bord de mer', 'Essaouira', 650, 3],
            ['Riad artisanal près des remparts', 'Essaouira', 800, 3],
        ];

        $annonces = collect($listings)->values()->map(function ($listing, $i) use ($hosts, $photos) {
            [$titre, $ville, $prix, $chambres] = $listing;

            return Annonce::create([
                'user_id' => $hosts->random()->id,
                'titre' => $titre,
                'description' => "Magnifique logement situé à {$ville}. Idéal pour découvrir la ville, proche des transports et des principaux points d'intérêt. Espace confortable, propre et parfaitement équipé pour un séjour réussi.",
                'adresse' => fake()->streetName() . ', ' . $ville,
                'ville' => $ville,
                'prix_par_nuit' => $prix,
                'nombre_de_chambres' => $chambres,
                'image' => $photos[$i % count($photos)],
            ]);
        });

        $statuses = ['pending', 'accepted', 'refused', 'cancelled'];

        foreach (range(1, 30) as $i) {
            $annonce = $annonces->random();
            $traveler = $travelers->random();

            if ($traveler->id === $annonce->user_id) {
                continue;
            }

            $status = $statuses[array_rand($statuses)];
            $startOffset = $status === 'accepted'
                ? fake()->numberBetween(-60, 10)
                : fake()->numberBetween(-30, 45);

            $start = Carbon::today()->addDays($startOffset);
            $end = (clone $start)->addDays(fake()->numberBetween(2, 7));

            $reservation = Reservation::create([
                'annonce_id' => $annonce->id,
                'user_id' => $traveler->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'total_price' => Reservation::calculateTotalPrice($start, $end, $annonce->prix_par_nuit),
                'status' => $status,
            ]);

            if ($status === 'accepted' && $end->isPast()) {
                Avis::create([
                    'reservation_id' => $reservation->id,
                    'user_id' => $traveler->id,
                    'rating' => fake()->numberBetween(3, 5),
                    'comment' => fake()->randomElement([
                        'Séjour parfait, logement conforme et très propre. Je recommande !',
                        'Très bon accueil, quartier idéal et hôte réactif.',
                        'Emplacement top, literie confortable. À refaire sans hésiter.',
                        'Bel appartement, bien équipé. Petit bémol sur le bruit la nuit.',
                        'Expérience géniale, exactement comme sur les photos.',
                    ]),
                ]);
            }
        }

        $this->command->info('Seed termine : ' . User::count() . ' utilisateurs, '
            . Annonce::count() . ' annonces, ' . Reservation::count() . ' reservations, '
            . Avis::count() . ' avis.');
        $this->command->info('Comptes de demo : admin@mini-rb.test / test@example.com (mot de passe : password)');
    }
}
