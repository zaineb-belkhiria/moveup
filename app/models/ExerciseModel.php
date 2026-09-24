<?php
/**
 * MOVEUP — ExerciseModel
 * Contient toute la bibliothèque d'exercices + logique métier pure.
 */
class ExerciseModel
{
    // ── Couleurs de difficulté ────────────────────────────────────────────────
    public static function getDiffColors(): array
    {
        return [
            'Débutant'      => '#34D399',
            'Intermédiaire' => '#F0A84A',
            'Avancé'        => '#F87171',
        ];
    }

    // ── Gestion des images : illustration SVG dédiée à chaque exercice ──────────
    public static function getExerciseImageUrl(string $exerciseName, string $categoryKey): string
    {
        $dir = '/assets/images/exercises/';

        // Une correspondance exacte nom d'exercice → illustration SVG.
        // Chaque exercice de la bibliothèque (getLibrary()) a son propre visuel.
        $map = [
            // Legs
            'Squats'                        => 'squat',
            'Fentes alternatives'           => 'lunges',
            'Glute Bridge'                  => 'glute-bridge',
            'Jump Squats'                   => 'jump-squat',
            'Calf Raises'                   => 'calf-raise',
            'Leg Press'                     => 'leg-press',
            'Leg Extension'                 => 'leg-extension',
            'Leg Curl couché'               => 'leg-curl',
            'Hack Squat'                    => 'hack-squat',
            'Romanian Deadlift'             => 'deadlift',

            // Chest
            'Pompes classiques'             => 'push-up',
            'Pompes diamant'                => 'diamond-push-up',
            'Pompes larges'                 => 'wide-push-up',
            'Pike Push-ups'                 => 'pike-push-up',
            'Développé couché plat'         => 'bench-press',
            'Développé incliné haltères'    => 'incline-press',
            'Écarté poulie basse'           => 'cable-fly',
            'Dips lestés'                   => 'dips',

            // Back
            'Superman'                      => 'superman',
            'Bird Dog'                      => 'bird-dog',
            'Rowing avec élastique'         => 'band-row',
            'Good Morning (sans charges)'   => 'good-morning',
            'Tractions (Pull-ups)'          => 'pull-up',
            'Tirage poulie haute'           => 'lat-pulldown',
            'Rowing barre'                  => 'barbell-row',
            'Face Pulls'                    => 'face-pull',

            // Shoulders
            'Pike Push-up'                  => 'pike-push-up',
            'Élévations latérales (haltères légers)' => 'lateral-raise',
            'Wall Handstand Hold'           => 'handstand',
            'Rotation externe avec élastique' => 'external-rotation',
            'Développé militaire haltères'  => 'shoulder-press',
            'Élévations latérales câble'    => 'lateral-raise',
            'Oiseau haltères (Rear Delt Fly)' => 'rear-delt',

            // Arms
            'Dips sur chaise'               => 'chair-dips',
            'Curl biceps avec bouteilles'   => 'biceps-curl',
            'Diamond Push-ups'              => 'diamond-push-up',
            'Hammer Curl (haltères)'        => 'hammer-curl',
            'Curl barre EZ'                 => 'ez-curl',
            'Extension triceps poulie haute' => 'triceps-extension',
            'Curl concentré haltère'        => 'concentration-curl',
            'Skull Crushers'                => 'skull-crusher',

            // Abs
            'Planche'                       => 'plank',
            'Crunch bicyclette'             => 'bicycle-crunch',
            'Mountain Climbers'             => 'mountain-climber',
            'Leg Raises'                    => 'leg-raises',
            'Ab Wheel Rollout'              => 'ab-wheel',
            'Crunch câble'                  => 'cable-crunch',
            'Leg Raises suspendu'           => 'hanging-leg-raise',
            'Pallof Press'                  => 'pallof-press',

            // Cardio
            'Jumping Jacks'                 => 'jumping-jacks',
            'Burpees'                       => 'burpees',
            'High Knees'                    => 'high-knees',
            'Box Jumps (sur canapé/marche)' => 'box-jump',
            'Mountain Climbers HIIT'        => 'mountain-climber',
            'Vélo elliptique'               => 'elliptical',
            'Tapis roulant HIIT'            => 'treadmill',
            'Rameur'                        => 'rowing-machine',
            'Corde à sauter'                => 'jump-rope',
        ];

        if (isset($map[$exerciseName])) {
            return View::base($dir . $map[$exerciseName] . '.jpg');
        }

        // Filet de sécurité : une icône générique par catégorie si un nom
        // ne correspond à aucune illustration connue.
        $fallback = [
            'legs'      => 'squat',
            'chest'     => 'push-up',
            'back'      => 'barbell-row',
            'shoulders' => 'shoulder-press',
            'arms'      => 'biceps-curl',
            'abs'       => 'plank',
            'cardio'    => 'jumping-jacks',
        ];
        return View::base($dir . ($fallback[$categoryKey] ?? 'squat') . '.jpg');
    }

    // ── Bibliothèque complète ────────────────────────────────────────────────
    public static function getLibrary(): array
    {
        return [

            // ═════════════════════════════════════════════════════════════════
            // LEGS
            // ═════════════════════════════════════════════════════════════════
            'legs' => [
                'label'   => 'Legs',
                'icon'    => '🦵',
                'color'   => '#C8F04A',
                'muscles' => 'Quadriceps · Ischio-jambiers · Fessiers · Mollets',
                'home'    => [
                    [
                        'nom'     => 'Squats',
                        'sets'    => '4 × 15 reps',
                        'muscles' => 'Quadriceps, fessiers',
                        'tips'    => 'Dos droit, genoux alignés sur les pieds, descendre jusqu\'à 90°. Pousser dans les talons à la remontée.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏋️',
                        'time'    => '12 min',
                    ],
                    [
                        'nom'     => 'Fentes alternatives',
                        'sets'    => '3 × 12 reps chaque jambe',
                        'muscles' => 'Quadriceps, fessiers, équilibre',
                        'tips'    => 'Grand pas en avant, genou arrière à 5 cm du sol. Garder le torse parfaitement vertical tout au long du mouvement.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🚶',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Glute Bridge',
                        'sets'    => '4 × 20 reps',
                        'muscles' => 'Fessiers, ischio-jambiers',
                        'tips'    => 'Allongé sur le dos, pieds à plat, pousser les hanches vers le haut. Contracter fort les fessiers en haut, tenir 1 seconde.',
                        'diff'    => 'Débutant',
                        'icon'    => '🔄',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Jump Squats',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Quadriceps, mollets, explosivité',
                        'tips'    => 'Squat normal, puis exploser vers le haut. Atterrir doucement sur la plante des pieds, genoux légèrement fléchis.',
                        'diff'    => 'Avancé',
                        'icon'    => '⚡',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Calf Raises',
                        'sets'    => '4 × 25 reps',
                        'muscles' => 'Mollets (gastrocnémiens)',
                        'tips'    => 'Debout sur le bord d\'une marche, monter sur la pointe des pieds lentement, descendre sous le niveau de la marche pour l\'étirement complet.',
                        'diff'    => 'Débutant',
                        'icon'    => '👟',
                        'time'    => '6 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Leg Press',
                        'sets'    => '4 × 12 reps',
                        'muscles' => 'Quadriceps, fessiers',
                        'tips'    => 'Pieds en largeur d\'épaules sur la plateforme. Descendre lentement sans verrouiller les genoux en haut. Dos plaqué contre le dossier.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏋️',
                        'time'    => '14 min',
                    ],
                    [
                        'nom'     => 'Leg Extension',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Quadriceps (isolation)',
                        'tips'    => 'Mouvement contrôlé, tenir 1 seconde en contraction maximale en haut, descendre lentement sur 3 secondes.',
                        'diff'    => 'Débutant',
                        'icon'    => '📐',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Leg Curl couché',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Ischio-jambiers',
                        'tips'    => 'Hanches plaquées sur le banc, ramener les talons vers les fessiers en contractant les ischio-jambiers. Descente lente et contrôlée.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔧',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Hack Squat',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Quadriceps, fessiers',
                        'tips'    => 'Pieds avancés sur la plateforme pour cibler davantage les quadriceps. Descente obligatoire à 90° minimum.',
                        'diff'    => 'Avancé',
                        'icon'    => '🔥',
                        'time'    => '14 min',
                    ],
                    [
                        'nom'     => 'Romanian Deadlift',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Ischio-jambiers, fessiers, bas du dos',
                        'tips'    => 'Dos neutre tout au long, barre proche des jambes, descendre jusqu\'à sentir l\'étirement profond des ischio-jambiers avant de remonter.',
                        'diff'    => 'Avancé',
                        'icon'    => '⚡',
                        'time'    => '15 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // CHEST
            // ═════════════════════════════════════════════════════════════════
            'chest' => [
                'label'   => 'Chest',
                'icon'    => '💪',
                'color'   => '#F0A84A',
                'muscles' => 'Pectoraux majeurs · Pectoraux mineurs · Deltoïdes antérieurs',
                'home'    => [
                    [
                        'nom'     => 'Pompes classiques',
                        'sets'    => '4 × 15 reps',
                        'muscles' => 'Pectoraux, triceps, épaules',
                        'tips'    => 'Corps gainé de la tête aux pieds, mains légèrement plus larges que les épaules. Descendre le torse jusqu\'à 2 cm du sol.',
                        'diff'    => 'Débutant',
                        'icon'    => '🔝',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Pompes diamant',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Pectoraux internes, triceps',
                        'tips'    => 'Index et pouces se touchent pour former un diamant sous la poitrine. Coudes longeant le corps à la descente.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '💎',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Pompes larges',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Pectoraux externes',
                        'tips'    => 'Mains très écartées (2× largeur d\'épaules). L\'écartement accentue l\'ouverture et l\'étirement des pectoraux externes.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '📏',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Pike Push-ups',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Deltoïdes antérieurs, pectoraux supérieurs',
                        'tips'    => 'Position en V inversé, hanches hautes. Descendre la tête vers le sol entre les mains. Cible surtout les épaules avant.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '⛰️',
                        'time'    => '8 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Développé couché plat',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Pectoraux, triceps, deltoïdes antérieurs',
                        'tips'    => 'Prise en pronation, légère cambrure naturelle du dos. Descendre la barre sur la poitrine, puis pousser jusqu\'à extension complète.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏋️',
                        'time'    => '18 min',
                    ],
                    [
                        'nom'     => 'Développé incliné haltères',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Pectoraux supérieurs',
                        'tips'    => 'Banc à 30-45°. Haltères en ligne avec les épaules en bas, légère rotation des poignets vers l\'intérieur à la remontée.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '📈',
                        'time'    => '14 min',
                    ],
                    [
                        'nom'     => 'Écarté poulie basse',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Pectoraux (étirement profond)',
                        'tips'    => 'Légère flexion des coudes fixe tout au long du mouvement. Contraction maximale en haut, descente lente pour un étirement profond.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔀',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Dips lestés',
                        'sets'    => '4 × 8 reps',
                        'muscles' => 'Pectoraux inférieurs, triceps',
                        'tips'    => 'Pencher légèrement le buste vers l\'avant pour cibler les pectoraux plutôt que les triceps. Descendre jusqu\'à l\'étirement maximal.',
                        'diff'    => 'Avancé',
                        'icon'    => '⬇️',
                        'time'    => '12 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // BACK
            // ═════════════════════════════════════════════════════════════════
            'back' => [
                'label'   => 'Back',
                'icon'    => '🔙',
                'color'   => '#60E0C8',
                'muscles' => 'Grand dorsal · Rhomboïdes · Trapèzes · Érecteurs du rachis',
                'home'    => [
                    [
                        'nom'     => 'Superman',
                        'sets'    => '4 × 15 reps',
                        'muscles' => 'Érecteurs du rachis, fessiers',
                        'tips'    => 'Allongé ventre à terre, bras tendus devant. Soulever simultanément bras et jambes, tenir la contraction 2 secondes en haut.',
                        'diff'    => 'Débutant',
                        'icon'    => '🦸',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Bird Dog',
                        'sets'    => '3 × 12 reps par côté',
                        'muscles' => 'Érecteurs du rachis, coordination, gainage',
                        'tips'    => 'À quatre pattes, dos horizontal. Étendre le bras droit et la jambe gauche en même temps, maintenir 2 secondes.',
                        'diff'    => 'Débutant',
                        'icon'    => '🐦',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Rowing avec élastique',
                        'sets'    => '4 × 12 reps',
                        'muscles' => 'Grand dorsal, rhomboïdes',
                        'tips'    => 'Fixer l\'élastique à hauteur de poitrine sur une porte. Tirer les coudes en arrière en serrant fort les omoplates ensemble.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔗',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Good Morning (sans charges)',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Érecteurs, ischio-jambiers, fessiers',
                        'tips'    => 'Debout, pieds en largeur d\'épaules. Pencher le buste en avant à 45° en gardant le dos parfaitement plat.',
                        'diff'    => 'Débutant',
                        'icon'    => '🌅',
                        'time'    => '8 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Tractions (Pull-ups)',
                        'sets'    => '4 × max reps',
                        'muscles' => 'Grand dorsal, biceps, rhomboïdes',
                        'tips'    => 'Prise pronation, tirer jusqu\'au menton au-dessus de la barre. Descente lente et contrôlée sur 3 secondes.',
                        'diff'    => 'Avancé',
                        'icon'    => '⬆️',
                        'time'    => '15 min',
                    ],
                    [
                        'nom'     => 'Tirage poulie haute',
                        'sets'    => '4 × 12 reps',
                        'muscles' => 'Grand dorsal, rhomboïdes',
                        'tips'    => 'Tirer vers le haut de la poitrine en ramenant les coudes dans les hanches. Éviter de se balancer. Descente lente 3 secondes.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '📡',
                        'time'    => '14 min',
                    ],
                    [
                        'nom'     => 'Rowing barre',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Dos épais, trapèzes, biceps',
                        'tips'    => 'Dos à 45°, barre proche des tibias, tirer jusqu\'au nombril en serrant les omoplates. Ne pas arrondir le dos.',
                        'diff'    => 'Avancé',
                        'icon'    => '🏋️',
                        'time'    => '16 min',
                    ],
                    [
                        'nom'     => 'Face Pulls',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Trapèzes moyens, deltoïdes postérieurs',
                        'tips'    => 'Poulie haute avec corde. Tirer vers le visage en ouvrant les coudes vers l\'extérieur et en tirant les mains vers les oreilles.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🎯',
                        'time'    => '10 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // SHOULDERS
            // ═════════════════════════════════════════════════════════════════
            'shoulders' => [
                'label'   => 'Shoulders',
                'icon'    => '⚡',
                'color'   => '#A78BFA',
                'muscles' => 'Deltoïdes antérieurs · Latéraux · Postérieurs · Trapèzes',
                'home'    => [
                    [
                        'nom'     => 'Pike Push-up',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Deltoïdes antérieurs, triceps',
                        'tips'    => 'Hanches hautes en V inversé, tête orientée vers le sol. Descendre le sommet du crâne vers les mains entre les pouces.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '📐',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Élévations latérales (haltères légers)',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Deltoïdes latéraux',
                        'tips'    => 'Légère flexion des coudes maintenue, monter à hauteur d\'épaule. Ne pas hausser les trapèzes. Descente lente 3 secondes.',
                        'diff'    => 'Débutant',
                        'icon'    => '↔️',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Wall Handstand Hold',
                        'sets'    => '3 × 20 secondes',
                        'muscles' => 'Deltoïdes (tout), gainage du tronc',
                        'tips'    => 'Mains à 15 cm du mur, corps vertical. Gainage total, regard entre les mains. Progresser chaque semaine.',
                        'diff'    => 'Avancé',
                        'icon'    => '🤸',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Rotation externe avec élastique',
                        'sets'    => '3 × 15 reps par côté',
                        'muscles' => 'Deltoïdes postérieurs, coiffe des rotateurs',
                        'tips'    => 'Coude collé au buste, avant-bras à 90°. Tirer l\'élastique vers l\'extérieur en rotation, tenir 1 seconde en fin de mouvement.',
                        'diff'    => 'Débutant',
                        'icon'    => '🔄',
                        'time'    => '8 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Développé militaire haltères',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Deltoïdes antérieurs et latéraux',
                        'tips'    => 'Assis, pousser les haltères au-dessus de la tête. Ne pas creuser le bas du dos. Coudes à 90° en position basse.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔝',
                        'time'    => '16 min',
                    ],
                    [
                        'nom'     => 'Élévations latérales câble',
                        'sets'    => '3 × 15 reps par côté',
                        'muscles' => 'Deltoïdes latéraux',
                        'tips'    => 'Tension constante grâce au câble tout au long du mouvement. Descente lente sur 3 secondes pour maximiser l\'étirement.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '↗️',
                        'time'    => '12 min',
                    ],
                    [
                        'nom'     => 'Oiseau haltères (Rear Delt Fly)',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Deltoïdes postérieurs',
                        'tips'    => 'Penché à 90°, bras légèrement fléchis fixés. Écarter les bras vers l\'arrière en serrant les omoplates ensemble.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🦅',
                        'time'    => '10 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // ARMS
            // ═════════════════════════════════════════════════════════════════
            'arms' => [
                'label'   => 'Arms',
                'icon'    => '💪',
                'color'   => '#FB923C',
                'muscles' => 'Biceps · Triceps · Avant-bras · Brachial',
                'home'    => [
                    [
                        'nom'     => 'Dips sur chaise',
                        'sets'    => '4 × 12 reps',
                        'muscles' => 'Triceps, pectoraux inférieurs',
                        'tips'    => 'Mains à l\'arrière d\'une chaise stable, doigts vers l\'avant. Descendre les fesses vers le sol en gardant le dos proche de la chaise.',
                        'diff'    => 'Débutant',
                        'icon'    => '🪑',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Curl biceps avec bouteilles',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Biceps brachial',
                        'tips'    => 'Coudes collés au corps, mouvement complet du bas jusqu\'en haut. Contraction forte en haut pendant 1 seconde.',
                        'diff'    => 'Débutant',
                        'icon'    => '💧',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Diamond Push-ups',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Triceps longue portion',
                        'tips'    => 'Mains en forme de diamant sous la poitrine. Coudes longeant le corps à la descente pour isoler les triceps.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '💎',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Hammer Curl (haltères)',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Brachial, biceps, avant-bras',
                        'tips'    => 'Prise neutre (pouce vers le haut), coudes fixes. Soulever en gardant les poignets droits, descente contrôlée.',
                        'diff'    => 'Débutant',
                        'icon'    => '🔨',
                        'time'    => '8 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Curl barre EZ',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Biceps, brachial',
                        'tips'    => 'Coudes fixés contre les hanches, supination complète du poignet. Phase excentrique (descente) sur 3 secondes.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '〰️',
                        'time'    => '12 min',
                    ],
                    [
                        'nom'     => 'Extension triceps poulie haute',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Triceps (toutes portions)',
                        'tips'    => 'Coudes fixés à côté du visage, extension complète des avant-bras vers le bas. Ne pas laisser les coudes s\'écarter.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔽',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Curl concentré haltère',
                        'sets'    => '3 × 12 reps par bras',
                        'muscles' => 'Biceps (pic)',
                        'tips'    => 'Assis, coude appuyé contre la cuisse intérieure. Curler en supinant le poignet vers le haut, contraction maximale en haut.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🎯',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Skull Crushers',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Triceps longue portion',
                        'tips'    => 'Allongé sur banc, barre EZ. Descendre la barre vers le front en gardant les coudes pointés au plafond. Remonter sans verrouiller.',
                        'diff'    => 'Avancé',
                        'icon'    => '💀',
                        'time'    => '12 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // ABS
            // ═════════════════════════════════════════════════════════════════
            'abs' => [
                'label'   => 'Abs',
                'icon'    => '🔥',
                'color'   => '#F472B6',
                'muscles' => 'Rectus abdominis · Obliques · Transverse · Fléchisseurs de hanche',
                'home'    => [
                    [
                        'nom'     => 'Planche',
                        'sets'    => '3 × 45 secondes',
                        'muscles' => 'Core entier, gainage',
                        'tips'    => 'Avant-bras au sol, corps aligné des talons à la nuque. Contracter abdominaux et fessiers. Ne pas laisser les hanches monter ou descendre.',
                        'diff'    => 'Débutant',
                        'icon'    => '📏',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Crunch bicyclette',
                        'sets'    => '3 × 20 reps alternées',
                        'muscles' => 'Obliques, abdominaux droits',
                        'tips'    => 'Allongé, genoux à 90°. Alterner en amenant le coude droit vers le genou gauche et vice-versa. Rotation du buste, pas des coudes.',
                        'diff'    => 'Débutant',
                        'icon'    => '🚴',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Mountain Climbers',
                        'sets'    => '3 × 30 secondes',
                        'muscles' => 'Core entier, fléchisseurs de hanche, cardio',
                        'tips'    => 'Position de pompe, ramener alternativement les genoux vers la poitrine rapidement. Garder les hanches basses et le dos plat.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏔️',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Leg Raises',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Abdominaux bas, fléchisseurs de hanche',
                        'tips'    => 'Allongé sur le dos, mains sous les fessiers. Lever les jambes tendues à 90°, descendre sans toucher le sol. Dos plaqué tout au long.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🦵',
                        'time'    => '8 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Ab Wheel Rollout',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Rectus abdominis, transverse',
                        'tips'    => 'À genoux, dérouler la roue jusqu\'à l\'extension maximale sans creuser les lombaires. Revenir lentement en contractant les abdominaux.',
                        'diff'    => 'Avancé',
                        'icon'    => '☸️',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Crunch câble',
                        'sets'    => '3 × 15 reps',
                        'muscles' => 'Abdominaux droits',
                        'tips'    => 'À genoux face à la poulie haute, mains près des oreilles. Enrouler le buste vers le bas en contractant les abdominaux, pas en tirant avec les bras.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🔗',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Leg Raises suspendu',
                        'sets'    => '3 × 12 reps',
                        'muscles' => 'Abdominaux bas, fléchisseurs de hanche',
                        'tips'    => 'Accroché à la barre de traction, monter les jambes à 90° sans balancer le corps. Tenir 1 seconde en haut, descendre lentement.',
                        'diff'    => 'Avancé',
                        'icon'    => '🎋',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Pallof Press',
                        'sets'    => '3 × 12 reps par côté',
                        'muscles' => 'Obliques, transverse (gainage anti-rotation)',
                        'tips'    => 'Poulie à hauteur de poitrine sur le côté. Pousser les bras devant soi et résister à la rotation. Maintenir le tronc parfaitement stable.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🎯',
                        'time'    => '10 min',
                    ],
                ],
            ],

            // ═════════════════════════════════════════════════════════════════
            // CARDIO
            // ═════════════════════════════════════════════════════════════════
            'cardio' => [
                'label'   => 'Cardio',
                'icon'    => '🏃',
                'color'   => '#34D399',
                'muscles' => 'Cardio-vasculaire · Corps entier · Endurance · Explosivité',
                'home'    => [
                    [
                        'nom'     => 'Jumping Jacks',
                        'sets'    => '3 × 1 minute',
                        'muscles' => 'Corps entier, système cardio-vasculaire',
                        'tips'    => 'Sauter en écartant jambes et bras simultanément, les mains se touchent au-dessus de la tête. Rythme régulier, atterrir doucement.',
                        'diff'    => 'Débutant',
                        'icon'    => '⭐',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Burpees',
                        'sets'    => '4 × 10 reps',
                        'muscles' => 'Corps entier, cardio HIIT',
                        'tips'    => 'Depuis debout : accroupi → pompe → saut avec genoux relevés et mains en l\'air. Mouvement fluide, explosif et sans pause entre les phases.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '💥',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'High Knees',
                        'sets'    => '3 × 45 secondes',
                        'muscles' => 'Fléchisseurs de hanche, mollets, cardio',
                        'tips'    => 'Courir sur place en montant les genoux à hauteur de hanche. Bras actifs, talons proches des fessiers. Maintenir un rythme élevé.',
                        'diff'    => 'Débutant',
                        'icon'    => '🦵',
                        'time'    => '8 min',
                    ],
                    [
                        'nom'     => 'Box Jumps (sur canapé/marche)',
                        'sets'    => '3 × 10 reps',
                        'muscles' => 'Puissance explosive, membres inférieurs',
                        'tips'    => 'Sauter sur une surface stable et sûre, atterrir pieds à plat avec genoux fléchis pour absorber. Descendre en marchant, pas en sautant.',
                        'diff'    => 'Avancé',
                        'icon'    => '📦',
                        'time'    => '10 min',
                    ],
                    [
                        'nom'     => 'Mountain Climbers HIIT',
                        'sets'    => '4 × 30 secondes',
                        'muscles' => 'Core, cuisses, cardio',
                        'tips'    => 'Position de pompe, alterner les genoux vers la poitrine le plus vite possible. Dos plat, hanches basses, rythme maximal.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏔️',
                        'time'    => '10 min',
                    ],
                ],
                'gym' => [
                    [
                        'nom'     => 'Vélo elliptique',
                        'sets'    => '25 minutes continu',
                        'muscles' => 'Corps entier, cardio basse intensité',
                        'tips'    => 'Résistance modérée. Maintenir 130-150 bpm (zone fat-burning). Utiliser les poignées mobiles pour engager les bras.',
                        'diff'    => 'Débutant',
                        'icon'    => '🚴',
                        'time'    => '30 min',
                    ],
                    [
                        'nom'     => 'Tapis roulant HIIT',
                        'sets'    => '8 × (30s sprint + 90s marche)',
                        'muscles' => 'Mollets, cuisses, cardio-vasculaire',
                        'tips'    => 'Sprint à 85-90% de votre fréquence cardiaque max. Récupération active à 50-60% FCmax. Total : ~20 minutes avec warm-up.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🏃',
                        'time'    => '25 min',
                    ],
                    [
                        'nom'     => 'Rameur',
                        'sets'    => '4 × 3 minutes',
                        'muscles' => 'Dos, jambes, bras, cardio',
                        'tips'    => 'Séquence : pousser avec les jambes d\'abord, puis incliner le buste en arrière, puis tirer les bras. Inverser à l\'envoi. 1 minute de repos entre les sets.',
                        'diff'    => 'Intermédiaire',
                        'icon'    => '🚣',
                        'time'    => '20 min',
                    ],
                    [
                        'nom'     => 'Corde à sauter',
                        'sets'    => '5 × 2 minutes',
                        'muscles' => 'Mollets, coordination, cardio',
                        'tips'    => 'Atterrir sur la plante des pieds, genoux légèrement fléchis. Poignets proches du corps. 1 minute de repos entre les rounds.',
                        'diff'    => 'Débutant',
                        'icon'    => '🪢',
                        'time'    => '15 min',
                    ],
                ],
            ],
        ];
    }

    // ── Méthodes d'accès ─────────────────────────────────────────────────────

    public static function getCategory(string $key): ?array
    {
        $lib = self::getLibrary();
        return $lib[$key] ?? null;
    }

    public static function categoryExists(string $key): bool
    {
        return array_key_exists($key, self::getLibrary());
    }

    public static function getAllKeys(): array
    {
        return array_keys(self::getLibrary());
    }
}