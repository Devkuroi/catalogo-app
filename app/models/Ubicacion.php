<?php
/**
 * Modelo de ubicaciones soportadas: Colombia, Italia y Francia.
 * Es la única fuente de datos para el autocompletado (JS) y la validación (PHP).
 */
class Ubicacion
{
    public const PAISES = [
        'Colombia' => [
            'codigo'          => 'CO',
            'documento'       => 'Cédula de ciudadanía',
            'documento_ayuda' => 'Solo números, entre 6 y 10 dígitos.',
            'documento_patron'=> '^[0-9]{6,10}$',
            'region'          => 'Departamento',
        ],
        'Italia' => [
            'codigo'          => 'IT',
            'documento'       => 'Codice Fiscale / Documento de Identidad',
            'documento_ayuda' => 'Codice Fiscale (16 caracteres) o documento de identidad (entre 8 y 16 caracteres alfanuméricos).',
            'documento_patron'=> '^[A-Za-z0-9]{8,16}$',
            'region'          => 'Región',
        ],
        'Francia' => [
            'codigo'          => 'FR',
            'documento'       => 'Carte Nationale d\'Identité / Passeport',
            'documento_ayuda' => 'Documento de identidad o pasaporte (entre 8 y 15 caracteres alfanuméricos).',
            'documento_patron'=> '^[A-Za-z0-9]{8,15}$',
            'region'          => 'Región',
        ],
    ];

    /** Ciudad => región, agrupadas por país. */
    private const CIUDADES = [
        'Colombia' => [
            'Bogotá' => 'Bogotá D.C.', 'Medellín' => 'Antioquia', 'Cali' => 'Valle del Cauca',
            'Barranquilla' => 'Atlántico', 'Cartagena' => 'Bolívar', 'Cúcuta' => 'Norte de Santander',
            'Soacha' => 'Cundinamarca', 'Soledad' => 'Atlántico', 'Bucaramanga' => 'Santander',
            'Bello' => 'Antioquia', 'Villavicencio' => 'Meta', 'Ibagué' => 'Tolima',
            'Santa Marta' => 'Magdalena', 'Valledupar' => 'Cesar', 'Montería' => 'Córdoba',
            'Pereira' => 'Risaralda', 'Manizales' => 'Caldas', 'Pasto' => 'Nariño',
            'Neiva' => 'Huila', 'Palmira' => 'Valle del Cauca', 'Popayán' => 'Cauca',
            'Buenaventura' => 'Valle del Cauca', 'Floridablanca' => 'Santander', 'Armenia' => 'Quindío',
            'Sincelejo' => 'Sucre', 'Itagüí' => 'Antioquia', 'Tumaco' => 'Nariño',
            'Envigado' => 'Antioquia', 'Dosquebradas' => 'Risaralda', 'Tuluá' => 'Valle del Cauca',
            'Barrancabermeja' => 'Santander', 'Riohacha' => 'La Guajira', 'Girón' => 'Santander',
            'Maicao' => 'La Guajira', 'Piedecuesta' => 'Santander', 'Tunja' => 'Boyacá',
            'Florencia' => 'Caquetá', 'Quibdó' => 'Chocó', 'Yopal' => 'Casanare',
            'Jamundí' => 'Valle del Cauca', 'Yumbo' => 'Valle del Cauca', 'Cartago' => 'Valle del Cauca',
            'Buga' => 'Valle del Cauca', 'Facatativá' => 'Cundinamarca', 'Zipaquirá' => 'Cundinamarca',
            'Chía' => 'Cundinamarca', 'Fusagasugá' => 'Cundinamarca', 'Girardot' => 'Cundinamarca',
            'Mosquera' => 'Cundinamarca', 'Funza' => 'Cundinamarca', 'Rionegro' => 'Antioquia',
            'Sabaneta' => 'Antioquia', 'Apartadó' => 'Antioquia', 'Turbo' => 'Antioquia',
            'Sogamoso' => 'Boyacá', 'Duitama' => 'Boyacá', 'Ipiales' => 'Nariño',
            'Magangué' => 'Bolívar', 'Ocaña' => 'Norte de Santander', 'Malambo' => 'Atlántico',
            'Arauca' => 'Arauca', 'Mocoa' => 'Putumayo', 'San Andrés' => 'San Andrés y Providencia',
            'Leticia' => 'Amazonas', 'San José del Guaviare' => 'Guaviare', 'Inírida' => 'Guainía',
            'Mitú' => 'Vaupés', 'Puerto Carreño' => 'Vichada',
        ],
        'Italia' => [
            'Roma' => 'Lacio', 'Milán' => 'Lombardía', 'Milano' => 'Lombardía', 'Nápoles' => 'Campania',
            'Napoli' => 'Campania', 'Turín' => 'Piamonte', 'Torino' => 'Piamonte', 'Palermo' => 'Sicilia',
            'Génova' => 'Liguria', 'Genova' => 'Liguria', 'Bolonia' => 'Emilia-Romaña', 'Bologna' => 'Emilia-Romaña',
            'Florencia' => 'Toscana', 'Firenze' => 'Toscana', 'Bari' => 'Apulia', 'Catania' => 'Sicilia',
            'Venecia' => 'Véneto', 'Venezia' => 'Véneto', 'Verona' => 'Véneto', 'Mesina' => 'Sicilia',
            'Messina' => 'Sicilia', 'Padua' => 'Véneto', 'Padova' => 'Véneto', 'Trieste' => 'Friuli-Venecia Julia',
            'Brescia' => 'Lombardía', 'Parma' => 'Emilia-Romaña', 'Tarento' => 'Apulia', 'Taranto' => 'Apulia',
            'Prato' => 'Toscana', 'Módena' => 'Emilia-Romaña', 'Modena' => 'Emilia-Romaña',
            'Reggio Calabria' => 'Calabria', 'Perugia' => 'Umbría', 'Rávena' => 'Emilia-Romaña',
            'Ravenna' => 'Emilia-Romaña', 'Livorno' => 'Toscana', 'Cagliari' => 'Cerdeña', 'Foggia' => 'Apulia',
            'Rímini' => 'Emilia-Romaña', 'Rimini' => 'Emilia-Romaña', 'Salerno' => 'Campania',
            'Ferrara' => 'Emilia-Romaña', 'Sassari' => 'Cerdeña', 'Monza' => 'Lombardía', 'Bérgamo' => 'Lombardía',
            'Bergamo' => 'Lombardía', 'Pescara' => 'Abruzos', 'Trento' => 'Trentino-Alto Adigio',
            'Siracusa' => 'Sicilia', 'Vicenza' => 'Véneto', 'Pisa' => 'Toscana', 'Siena' => 'Toscana',
        ],
        'Francia' => [
            'París' => 'Isla de Francia', 'Paris' => 'Isla de Francia', 'Marsella' => 'Provenza-Alpes-Costa Azul',
            'Marseille' => 'Provenza-Alpes-Costa Azul', 'Lyon' => 'Auvernia-Ródano-Alpes', 'Toulouse' => 'Occitania',
            'Niza' => 'Provenza-Alpes-Costa Azul', 'Nice' => 'Provenza-Alpes-Costa Azul', 'Nantes' => 'Países del Loira',
            'Estrasburgo' => 'Gran Este', 'Strasbourg' => 'Gran Este', 'Montpellier' => 'Occitania',
            'Burdeos' => 'Nueva Aquitania', 'Bordeaux' => 'Nueva Aquitania', 'Lille' => 'Alta Francia',
            'Rennes' => 'Bretaña', 'Reims' => 'Gran Este', 'Tolón' => 'Provenza-Alpes-Costa Azul',
            'Toulon' => 'Provenza-Alpes-Costa Azul', 'Saint-Étienne' => 'Auvernia-Ródano-Alpes',
            'El Havre' => 'Normandía', 'Le Havre' => 'Normandía', 'Grenoble' => 'Auvernia-Ródano-Alpes',
            'Dijon' => 'Borgoña-Franco Condado', 'Angers' => 'Países del Loira', 'Villeurbanne' => 'Auvernia-Ródano-Alpes',
            'Nimes' => 'Occitania', 'Nîmes' => 'Occitania', 'Clermont-Ferrand' => 'Auvernia-Ródano-Alpes',
            'Aix-en-Provence' => 'Provenza-Alpes-Costa Azul', 'Brest' => 'Bretaña', 'Tours' => 'Centro-Valle de Loira',
            'Amiens' => 'Alta Francia', 'Limoges' => 'Nueva Aquitania', 'Annecy' => 'Auvernia-Ródano-Alpes',
            'Perpiñán' => 'Occitania', 'Perpignan' => 'Occitania', 'Metz' => 'Gran Este',
            'Besanzón' => 'Borgoña-Franco Condado', 'Besançon' => 'Borgoña-Franco Condado',
            'Ruan' => 'Normandía', 'Rouen' => 'Normandía', 'Cannes' => 'Provenza-Alpes-Costa Azul',
        ],
    ];

    public static function paises(): array
    {
        return array_keys(self::PAISES);
    }

    public static function esPaisValido(string $pais): bool
    {
        return isset(self::PAISES[$pais]);
    }

    /** Lista plana: [['ciudad'=>..., 'region'=>..., 'pais'=>...], ...] */
    public static function ciudades(): array
    {
        $lista = [];
        foreach (self::CIUDADES as $pais => $ciudades) {
            foreach ($ciudades as $ciudad => $region) {
                $lista[] = ['ciudad' => $ciudad, 'region' => $region, 'pais' => $pais];
            }
        }
        usort($lista, fn($a, $b) => strcmp(self::normalizar($a['ciudad']), self::normalizar($b['ciudad'])));
        return $lista;
    }

    /**
     * Devuelve todas las coincidencias exactas (sin tildes ni mayúsculas) de una ciudad.
     * Puede haber más de una: "Florencia" existe en Colombia y en Italia.
     */
    public static function buscar(string $ciudad): array
    {
        $clave = self::normalizar($ciudad);
        return array_values(array_filter(
            self::ciudades(),
            fn($c) => self::normalizar($c['ciudad']) === $clave
        ));
    }

    /** "  ROMA " => "roma", "BOGOTÁ" => "bogota" */
    public static function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'à' => 'a', 'è' => 'e', 'ò' => 'o', 'ï' => 'i',
            'ç' => 'c', 'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o',
            'û' => 'u', 'ë' => 'e',
        ]);
        return preg_replace('/\s+/', ' ', $texto);
    }
}
