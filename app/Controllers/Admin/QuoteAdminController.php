<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Quote;

/**
 * Admin Quote Controller - CRUD pro citace
 */
class QuoteAdminController extends Controller
{
    private Quote $quoteModel;

    public function __construct()
    {
        parent::__construct();

        // Vyžaduje autentizaci
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $this->quoteModel = new Quote();
    }

    /**
     * Seznam všech citací
     */
    public function index(): void
    {
        $quotes = $this->quoteModel->getAll();

        $this->view('admin/quotes/index', [
            'title' => 'Citace',
            'quotes' => $quotes
        ], 'admin');
    }

    /**
     * Zobrazení formuláře pro vytvoření nové citace
     */
    public function create(): void
    {
        $this->view('admin/quotes/form', [
            'title' => 'Nová citace',
            'quote' => null,
            'translations' => []
        ], 'admin');
    }

    /**
     * Uložení nové citace
     */
    public function store(): void
    {
        // Validace CSRF tokenu
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Neplatný CSRF token');
            redirect(adminUrl('quotes/create'));
        }

        // Validace vstupů
        $errors = [];

        if (empty($_POST['text_cs'])) {
            $errors[] = 'České znění citace je povinné';
        }
        if (empty($_POST['author_cs'])) {
            $errors[] = 'Autor (CS) je povinný';
        }
        if (empty($_POST['text_en'])) {
            $errors[] = 'Anglické znění citace je povinné';
        }
        if (empty($_POST['author_en'])) {
            $errors[] = 'Autor (EN) je povinný';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(', ', $errors));
            $_SESSION['old_input'] = $_POST;
            redirect(adminUrl('quotes/create'));
        }

        try {
            // Vytvoření citace s překlady
            $quoteId = $this->quoteModel->createWithTranslations(
                [
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'display_order' => (int)($_POST['display_order'] ?? 0)
                ],
                [
                    'cs' => [
                        'text' => $_POST['text_cs'],
                        'author' => $_POST['author_cs'],
                        'role' => $_POST['role_cs'] ?? null
                    ],
                    'en' => [
                        'text' => $_POST['text_en'],
                        'author' => $_POST['author_en'],
                        'role' => $_POST['role_en'] ?? null
                    ]
                ]
            );

            if ($quoteId) {
                Session::flash('success', 'Citace byla úspěšně vytvořena');
                redirect(adminUrl('quotes'));
            } else {
                throw new \Exception('Nepodařilo se vytvořit citaci');
            }

        } catch (\Exception $e) {
            Session::flash('error', 'Chyba při vytváření citace: ' . $e->getMessage());
            $_SESSION['old_input'] = $_POST;
            redirect(adminUrl('quotes/create'));
        }
    }

    /**
     * Zobrazení formuláře pro úpravu citace
     */
    public function edit(int $id): void
    {
        $data = $this->quoteModel->findWithTranslations($id);

        if (!$data) {
            Session::flash('error', 'Citace nenalezena');
            redirect(adminUrl('quotes'));
        }

        $this->view('admin/quotes/form', [
            'title' => 'Upravit citaci',
            'quote' => $data['quote'],
            'translations' => $data['translations']
        ], 'admin');
    }

    /**
     * Aktualizace citace
     */
    public function update(int $id): void
    {
        // Validace CSRF tokenu
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Neplatný CSRF token');
            redirect(adminUrl("quotes/{$id}/edit"));
        }

        // Validace vstupů
        $errors = [];

        if (empty($_POST['text_cs'])) {
            $errors[] = 'České znění citace je povinné';
        }
        if (empty($_POST['author_cs'])) {
            $errors[] = 'Autor (CS) je povinný';
        }
        if (empty($_POST['text_en'])) {
            $errors[] = 'Anglické znění citace je povinné';
        }
        if (empty($_POST['author_en'])) {
            $errors[] = 'Autor (EN) je povinný';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(', ', $errors));
            $_SESSION['old_input'] = $_POST;
            redirect(adminUrl("quotes/{$id}/edit"));
        }

        try {
            $updated = $this->quoteModel->updateWithTranslations(
                $id,
                [
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'display_order' => (int)($_POST['display_order'] ?? 0)
                ],
                [
                    'cs' => [
                        'text' => $_POST['text_cs'],
                        'author' => $_POST['author_cs'],
                        'role' => $_POST['role_cs'] ?? null
                    ],
                    'en' => [
                        'text' => $_POST['text_en'],
                        'author' => $_POST['author_en'],
                        'role' => $_POST['role_en'] ?? null
                    ]
                ]
            );

            if ($updated) {
                Session::flash('success', 'Citace byla úspěšně aktualizována');
                redirect(adminUrl('quotes'));
            } else {
                throw new \Exception('Nepodařilo se aktualizovat citaci');
            }

        } catch (\Exception $e) {
            Session::flash('error', 'Chyba při aktualizaci citace: ' . $e->getMessage());
            redirect(adminUrl("quotes/{$id}/edit"));
        }
    }

    /**
     * Smazání citace
     */
    public function delete(int $id): void
    {
        // Validace CSRF tokenu
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Neplatný CSRF token');
            redirect(adminUrl('quotes'));
        }

        try {
            $this->quoteModel->deleteQuote($id);
            Session::flash('success', 'Citace byla úspěšně smazána');
        } catch (\Exception $e) {
            Session::flash('error', 'Chyba při mazání citace: ' . $e->getMessage());
        }

        redirect(adminUrl('quotes'));
    }

    /**
     * Přepnutí aktivního stavu citace (AJAX)
     */
    public function toggleActive(int $id): void
    {
        try {
            $quote = $this->quoteModel->find($id);
            if (!$quote) {
                throw new \Exception('Citace nenalezena');
            }

            $newStatus = $quote->is_active ? 0 : 1;
            $this->quoteModel->update($id, ['is_active' => $newStatus]);

            Session::flash('success', 'Stav citace byl změněn');
        } catch (\Exception $e) {
            Session::flash('error', 'Chyba při změně stavu: ' . $e->getMessage());
        }

        redirect(adminUrl('quotes'));
    }
}
