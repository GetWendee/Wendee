<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'contrats'])

<style>
.wd-contrats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(340px,1fr));
    gap:16px;
}
.wd-contrat-card{
    background:white;
    border:1px solid var(--line);
    border-radius:12px;
    padding:20px;
}
.wd-contrat-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
}
.wd-contrat-provider{
    display:flex;
    align-items:center;
    gap:12px;
}
.wd-contrat-logo{
    width:38px;
    height:38px;
    border-radius:9px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:800;
    color:#fff;
    flex:0 0 auto;
}
.wd-contrat-titre{
    font-size:15px;
    font-weight:700;
    color:var(--ink);
}
.wd-contrat-numero{
    font-size:12px;
    color:var(--muted);
    margin-top:1px;
}
.wd-contrat-badge{
    font-size:11px;
    font-weight:700;
    padding:4px 10px;
    border-radius:999px;
    white-space:nowrap;
}
.wd-contrat-badge.expire{
    background:#fbf1e4;
    color:#9a6a26;
}
.wd-contrat-badge.actif{
    background:#e9f4ec;
    color:var(--green);
}
.wd-contrat-dates{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
    margin:16px 0;
}
.wd-contrat-date{
    border:1px solid var(--line);
    border-radius:9px;
    padding:10px 12px;
}
.wd-contrat-date span{
    display:block;
    font-size:10px;
    font-weight:700;
    letter-spacing:.06em;
    color:var(--muted);
    text-transform:uppercase;
    margin-bottom:3px;
}
.wd-contrat-date strong{
    font-size:13px;
    color:var(--ink);
}
.wd-contrat-foot{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-top:14px;
    border-top:1px solid var(--line);
    font-size:13px;
    color:var(--muted);
}
.wd-contrat-foot b{
    color:var(--ink);
}
.wd-contrat-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:var(--pink);
    color:#fff;
    font-size:12px;
    font-weight:700;
    padding:8px 16px;
    border-radius:8px;
    text-decoration:none;
}
</style>

<section class="wd-section">
    <div class="wd-contrats-grid">

        <div class="wd-contrat-card">
            <div class="wd-contrat-head">
                <div class="wd-contrat-provider">
                    <div class="wd-contrat-logo" style="background:#d40057;">SL</div>
                    <div>
                        <div class="wd-contrat-titre">Assurance Vie</div>
                        <div class="wd-contrat-numero">N°5656512455484</div>
                    </div>
                </div>
                <span class="wd-contrat-badge expire">Expiré</span>
            </div>
            <div class="wd-contrat-dates">
                <div class="wd-contrat-date">
                    <span>Délivré le</span>
                    <strong>8 juillet 2026</strong>
                </div>
                <div class="wd-contrat-date">
                    <span>Échéance</span>
                    <strong>8 août 2026</strong>
                </div>
            </div>
            <div class="wd-contrat-foot">
                <span>Proposé par : <b>Swiss Life</b></span>
                <a href="https://adfs.swisslife.fr/adfs/ls/?SAMLRequest=fZJNb8IwDIb%2FSpU7TVqglIhWYqBpSGxD0O2wy5QGd0QKSRen%2B%2Fj3a8sm2IVr8vqx%2FcgzFEdd83njD2YL7w2gD76O2iDvPzLSOMOtQIXciCMg95Lv5vdrHoeM1856K60mwRwRnFfWLKzB5ghuB%2B5DSXjarjNy8L5GTimiDfFTIWpVgTUQVo52XSjWtEVVSgOtLXoqJJJg2Y6ijOigZ4TYV3hmdIDuhWqkJFgtM%2FI6jOJplCQQTcpKDtk%2BSct0lEpZJdEwYZGYjkdlCW0YsYGVQS%2BMz0jM4mTAJgOWFizl4ykfspCx%2BIUEt9ZJ6O1kpBIa29LN79Y3yuyVebuuqDyFkN8VxWawedwVJHgGh%2F1WbYDks04B7%2BdxF%2BqvY8Wfb5JfsTujF%2BxTo5o%2FtLDVcmO1kt%2FBXGv7uXAgPGTEuwYIzU9V%2F28i%2FwE%3D" target="_blank" rel="noopener" class="wd-contrat-btn">Accéder →</a>
            </div>
        </div>

        <div class="wd-contrat-card">
            <div class="wd-contrat-head">
                <div class="wd-contrat-provider">
                    <div class="wd-contrat-logo" style="background:#1b1716;">CNP</div>
                    <div>
                        <div class="wd-contrat-titre">Plan d'Épargne Retraite</div>
                        <div class="wd-contrat-numero">N°56565365565</div>
                    </div>
                </div>
                <span class="wd-contrat-badge actif">Actif</span>
            </div>
            <div class="wd-contrat-dates">
                <div class="wd-contrat-date">
                    <span>Délivré le</span>
                    <strong>6 mai 2026</strong>
                </div>
                <div class="wd-contrat-date">
                    <span>Échéance</span>
                    <strong>13 mai 2039</strong>
                </div>
            </div>
            <div class="wd-contrat-foot">
                <span>Proposé par : <b>CNP Assurances</b></span>
                <a href="https://idg.cnp.fr/idg/XUI/?realm=/partenaires&authIndexType=service&authIndexValue=authArcade&goto=https://idg.cnp.fr:443/idg/oauth2/realms/root/realms/partenaires/authorize?client_id%3Dkhp-front-partenaires-production-vNiGmk9QpRoRuwhX9wwEMG79uYL8O4RV08PRJoIE%26redirect_uri%3Dhttps://univers-patrimoine.cnp.fr/khp/%26response_type%3Dcode%26scope%3Dopenid%2520profile%2520droit_khp%2520droit_kar%2520droit_ecp%2520droit_kat%2520droit_pfn%2520droit_pga%2520droit_kal%26nonce%3D4fe50e8472fa5f5d13825473f70ecb2c04VJTX2ay%26state%3Dc7dca15ff27ddc11c1c2758c30c1dcfbbcjxXFpkj%26code_challenge%3D486eycwKSrGE4gJHtzYoKku1_oB9NFagsNfrWMw1DwQ%26code_challenge_method%3DS256%26service%3DauthArcade%26sso%3Dfalse#login/" target="_blank" rel="noopener" class="wd-contrat-btn">Accéder →</a>
            </div>
        </div>

    </div>
</section>

</x-tenant-app-layout>
