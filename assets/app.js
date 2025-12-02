/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import 'bootstrap';
// any CSS you import will output into a single css file (app.css in this case)
import './styles/bootstrap.scss'
import './styles/app.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// search bar
document.addEventListener('DOMContentLoaded', () => {
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
});
