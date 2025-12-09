/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;


document.addEventListener('DOMContentLoaded', () => {
    // search bar
    const toggleBtn = document.getElementById('searchToggle');
    const searchForm = document.getElementById('searchForm');

    if (toggleBtn && searchForm) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = searchForm.style.display === 'none' || searchForm.style.display === '';

            if (isHidden) {
                searchForm.style.display = 'flex';
                const input = searchForm.querySelector('input');
                if (input) {
                    input.focus();
                }
            } else {
                searchForm.style.display = 'none';
            }
        });
    }

    //toast add watchlist
    const btn    = document.getElementById('btn-add-to-watchlist');
    const select = document.getElementById('watchlist-select');
    const input  = document.getElementById('new-watchlist-name');
    const modalEl = document.getElementById('watchlistModal');
    const toastEl = document.getElementById('watchlist-toast');

    if (!btn) return;

    btn.addEventListener('click', async (e) => {
        e.preventDefault();

        const tmdbId     = btn.dataset.tmdbId;
        const existingId = select.value;
        const newName    = input.value.trim();

        if (!existingId && !newName) {
            alert('Choisis une liste ou entre un nom.');
            return;
        }

        let url    = btn.dataset.baseUrl;
        const listId = existingId || 0;
        url = url.replace('__ID__', listId);

        const response = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                tmdbId: tmdbId,
                type: 'movie',
                newListName: existingId ? null : newName
            }),
        });

        const data = await response.json();
        if (response.ok) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();

            if (data.createdListId) {
                // location.reload();
            }
        } else {
            alert('Erreur : ' + (data.error ?? 'inconnue'));
        }
    });


});
