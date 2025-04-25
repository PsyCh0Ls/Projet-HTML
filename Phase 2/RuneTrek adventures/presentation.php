<?php
session_start();
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>
<div class="presentation-page">
    <main>
        <!-- Section de recherche rapide -->
        <section class="quick-search">
            <div class="search-container">
                <h2>Trouver une aventure</h2>
                <form class="search-form" action="search.php" method="GET">
                    <div class="search-input-group">
                        <select name="region[]" required>
                            <option value="" disabled selected>Choisir une région...</option>
                            <option value="piltover">Piltover</option>
                            <option value="demacia">Demacia</option>
                            <option value="ionia">Ionia</option>
                            <option value="freljord">Freljord</option>
                            <option value="zaun">Zaun</option>
                            <option value="noxus">Noxus</option>
                            <option value="bilgewater">Bilgewater</option>
                            <option value="shurima">Shurima</option>
                            <option value="targon">Targon</option>
                            <option value="îles obscures">Îles Obscures</option>
                            <option value="bandle city">Bandle City</option>
                            <option value="ixtal">Ixtal</option>
                            <option value="faille de l'invocateur">Faille de l’Invocateur</option>
                            <option value="icathia">Icathia</option>
                            <option value="runeterra">Runeterra</option>
                        </select>
                        <input type="date" name="start_date" id="date" placeholder="Date de départ">
                        <input type="number" name="duration" id="duration" placeholder="Durée (jours)" min="1" max="30">
                        <button type="submit" class="search-button">Rechercher</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Section de présentation -->
        <section class="about-section">
            <h1>Bienvenue chez RuneTrek Adventures</h1>
            <p class="lead-text">Votre portail vers les merveilles mystiques de Runeterra</p>
            
            <div class="about-grid">
                <div class="about-content">
                    <h2>Notre Histoire</h2>
                    <p>Fondée par d'anciens explorateurs de Piltover, RuneTrek Adventures s'est donné pour mission de faire découvrir les merveilles de Runeterra aux aventuriers du monde entier. Notre expertise unique et nos connections locales vous garantissent une expérience authentique et sécurisée.</p>
                </div>
                <div class="about-image history-image"></div>
            </div>

            <div class="about-grid reverse">
                <div class="about-content">
                    <h2>Notre Philosophie</h2>
                    <p>Nous croyons que chaque région de Runeterra recèle des trésors uniques qui méritent d'être découverts. De la technologie hextech de Piltover aux jardins spirituels d'Ionia, nous créons des itinéraires qui respectent les cultures locales tout en offrant des aventures inoubliables.</p>
                </div>
                <div class="about-image philosophy-image"></div>
            </div>

            <!-- Section "Notre équipe" -->
            <div class="team">
                <h2>Notre équipe</h2>
                <p>Rencontrez nos guides passionnés qui vous accompagneront dans vos aventures.</p>
                <div class="team-members">
                    <div class="team-member">
                        <img src="images/guide1.jpg" alt="Guide 1">
                        <h4>Guide 1</h4>
                        <p>Spécialiste de Piltover et Zaun.</p>
                    </div>
                    <div class="team-member">
                        <img src="images/guide2.jpg" alt="Guide 2">
                        <h4>Guide 2</h4>
                        <p>Expert du Freljord et de Demacia.</p>
                    </div>
                    <div class="team-member">
                        <img src="images/guide3.jpg" alt="Guide 3">
                        <h4>Guide 3</h4>
                        <p>Connaisseur de Shurima et Targon.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section des engagements -->
        <section class="commitments">
            <h2>Nos Engagements</h2>
            <div class="commitment-grid">
                <div class="commitment-card">
                    <div class="commitment-icon safety"></div>
                    <h3>Sécurité Maximale</h3>
                    <p>Escortes qualifiées et itinéraires sécurisés sur toutes nos destinations</p>
                </div>
                <div class="commitment-card">
                    <div class="commitment-icon culture"></div>
                    <h3>Respect des Cultures</h3>
                    <p>Immersion authentique dans le respect des traditions locales</p>
                </div>
                <div class="commitment-card">
                    <div class="commitment-icon quality"></div>
                    <h3>Qualité Premium</h3>
                    <p>Hébergements soigneusement sélectionnés et services haut de gamme</p>
                </div>
            </div>
        </section>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
