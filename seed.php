<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Models\Book;
use App\Models\Blog;

/**
 * ARCHIVAL SEEDER
 * Populates the database with initial scholarly entries and library items.
 */

echo "--- SCHOLARLY ARCHIVE SEEDER ---\n";

$bookModel = new Book();
$blogModel = new Blog();

// 1. Seed Books
$books = [
    [
        'title' => 'The Vellum Dialogues',
        'slug' => 'the-vellum-dialogues',
        'description' => 'A foundational text on the preservation of 16th-century manuscripts and the ethics of digital restoration.',
        'price' => 45.00,
        'stock' => 12,
        'category' => 'MONOGRAPH',
        'format' => 'Hardcover',
        'cover_image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Architectural Syntax',
        'slug' => 'architectural-syntax',
        'description' => 'Exploring the mathematical patterns in Gothic cathedral layouts and their influence on modern grid systems.',
        'price' => 58.00,
        'stock' => 8,
        'category' => 'ARCHIVAL SERIES',
        'format' => 'Hardcover',
        'cover_image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Shadow & Ink',
        'slug' => 'shadow-and-ink',
        'description' => 'A limited press edition detailing the history of iron gall ink and the secrets of the Scriptorium.',
        'price' => 32.50,
        'stock' => 20,
        'category' => 'LIMITED EDITION',
        'format' => 'Softcover',
        'cover_image' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'The Typography of Silence',
        'slug' => 'the-typography-of-silence',
        'description' => 'An essay collection on the importance of whitespace in both physical and digital typesetting.',
        'price' => 19.99,
        'stock' => 50,
        'category' => 'ESSAY COLLECTION',
        'format' => 'Digital',
        'cover_image' => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'The Gutenberg Legacy',
        'slug' => 'the-gutenberg-legacy',
        'description' => 'An investigation into the technical innovations of the movable type press and its sociopolitical impact on 15th-century Europe.',
        'price' => 42.00,
        'stock' => 15,
        'category' => 'HISTORICAL ANALYSIS',
        'format' => 'Hardcover',
        'cover_image' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Metaphysics of the Grid',
        'slug' => 'metaphysics-of-the-grid',
        'description' => 'A philosophical inquiry into the structural rigidity of modern information design and the search for organic balance.',
        'price' => 38.50,
        'stock' => 25,
        'category' => 'PHILOSOPHY',
        'format' => 'Softcover',
        'cover_image' => 'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'The Lost Folio',
        'slug' => 'the-lost-folio',
        'description' => 'A reconstruction of the missing fragments from the 14th-century "Codex Borealis", featuring high-resolution plate reproductions.',
        'price' => 75.00,
        'stock' => 5,
        'category' => 'FACSIMILE EDITION',
        'format' => 'Large Format Hardcover',
        'cover_image' => 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?auto=format&fit=crop&w=800&q=80'
    ]
];

echo "Seeding Books...\n";
foreach ($books as $bookData) {
    try {
        if ($bookModel->create($bookData)) {
            echo " [+] Seeded: {$bookData['title']}\n";
        } else {
            echo " [!] Failed: {$bookData['title']}\n";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo " [-] Skipped: {$bookData['title']} (Already exists)\n";
        } else {
            echo " [!] Error: " . $e->getMessage() . "\n";
        }
    }
}

// 2. Seed Blogs
$blogs = [
    [
        'title' => 'The Oxford Margins',
        'slug' => 'the-oxford-margins',
        'tagline' => 'A technical deep-dive into the relics of 16th-century manuscript margins.',
        'content' => '<p>The scholarly margin is not merely empty space; it is a battleground of annotations, a history of reading itself. In this essay, we explore why wide margins are essential for the intellectual breath...</p>',
        'status' => 'published',
        'cover_image' => 'https://images.unsplash.com/photo-1513001900722-370f803f498d?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Ink & Algorithms',
        'slug' => 'ink-and-algorithms',
        'tagline' => 'Can artificial intelligence ever replicate the nuanced touch of a scholarly editor?',
        'content' => '<p>As we transition from the weighted touch of the pen to the weightless logic of the algorithm, what is lost? This reflection examines the soul of the archive in a digital age.</p>',
        'status' => 'published',
        'cover_image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'The Scriptorium Secrets',
        'slug' => 'scriptorium-secrets',
        'tagline' => 'Hidden notes found in the back-alleys of archival history.',
        'content' => '<p>Every archive has a basement, and every basement has a secret. This week, we uncover the mystery of the missing monographs from the 1884 catalog.</p>',
        'status' => 'draft',
        'cover_image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'The Silence of the Scriptorium',
        'slug' => 'the-silence-of-the-scriptorium',
        'tagline' => 'Evaluating the acoustic properties of medieval writing rooms.',
        'content' => '<p>Silence was not merely a rule in the scriptorium; it was a tool. The repetitive scratching of the quill created a rhythmic white noise that induced a state of deep focus equivalent to modern "deep work" practices.</p>',
        'status' => 'published',
        'cover_image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => 'Deciphering the Digital Void',
        'slug' => 'deciphering-the-digital-void',
        'tagline' => 'What happens to scholarly archives when the servers go dark?',
        'content' => '<p>We are currently living in the "Digital Dark Age." The fragility of cloud storage compared to the resilience of vellum is a paradox of our time. This essay proposes a hybrid physical-digital storage strategy...</p>',
        'status' => 'published',
        'cover_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=800&q=80'
    ],
    [
        'title' => "The Curator's Desk: Autumn 2026",
        'slug' => 'curators-desk-autumn-2026',
        'tagline' => 'A seasonal update on archive acquisitions and scholarly milestones.',
        'content' => '<p>The shifting light of autumn brings a renewed focus to the archive. This season, we have acquired three new facsimiles and begun a collaboration with the Royal Typographic Society.</p>',
        'status' => 'published',
        'cover_image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=800&q=80'
    ]
];

echo "\nSeeding Blog Entries...\n";
foreach ($blogs as $blogData) {
    try {
        if ($blogModel->create($blogData)) {
            echo " [+] Seeded: {$blogData['title']} ({$blogData['status']})\n";
        } else {
            echo " [!] Failed: {$blogData['title']}\n";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo " [-] Skipped: {$blogData['title']} (Already exists)\n";
        } else {
            echo " [!] Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n--- SEEDING COMPLETE ---\n";
