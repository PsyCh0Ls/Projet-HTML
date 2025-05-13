<?php
include 'includes/header.php';
?>

<div class="presentation-page">
    <main>
        <!-- Section de recherche rapide -->
        <section class="quick-search">
            <div class="search-container">
                <h2>Trouver une Aventure</h2>
                <form action="search.php" method="get" class="search-form">
                    <div class="search-input-group">
                        <select name="region" class="search-input">
                            <option value="">Choisir une région...</option>
                            <option value="piltover">Piltover</option>
                            <option value="demacia">Demacia</option>
                            <option value="freljord">Freljord</option>
                            <option value="ionia">Ionia</option>
                            <option value="noxus">Noxus</option>
                        </select>
                        <input type="date" name="date" class="search-input" placeholder="jj/mm/aaaa">
                        <input type="number" name="duration" class="search-input" placeholder="Durée (jours)" min="1">
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
                    <h2>Notre Philosophie</h2>
                    <p>Nous croyons que chaque région de Runeterra recèle des trésors uniques qui méritent d’être découverts. De la technologie hextech de Piltover aux jardins spirituels d’Ionia, nous créons des itinéraires qui respectent les cultures locales tout en offrant des aventures inoubliables.</p>
                </div>
                <div class="about-image philosophy-image"></div>
            </div>

            <!-- Section "Nos Guides" -->
            <div class="team">
                <h2>Nos Guides</h2>
                <p>Rencontrez nos guides passionnés qui vous accompagneront dans vos aventures.</p>
                <div class="team-members">
                    <div class="team-member">
                        <img src="images/piltover.jpg" alt="Guide 1">
                        <h4>Guide 1</h4>
                        <p>Spécialiste de Piltover et Zaun</p>
                        <a href="booking.php?trip=piltover" class="view-details">Découvrir</a>
                    </div>
                    <div class="team-member">
                        <img src="images/demacia.jpg" alt="Guide 2">
                        <h4>Guide 2</h4>
                        <p>Expert de Freljord et Demacia</p>
                        <a href="booking.php?trip=demacia" class="view-details">Découvrir</a>
                    </div>
                    <div class="team-member">
                        <img src="images/frejlord.jpg" alt="Guide 3">
                        <h4>Guide 3</h4>
                        <p>Connaisseur de Shurima et Targon</p>
                        <a href="booking.php?trip=frejlord" class="view-details">Découvrir</a>
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

<?php
include 'includes/footer.php';
?>