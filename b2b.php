<?php
session_start();

// 1. Vaša databáza povolených B2B partnerov (E-mail => Heslo)
// Tu si môžete pridávať ďalších partnerov podľa potreby
$allowed_users = [
    "partner@rybarstvo.cz" => "heslo123",
    "obchod@firma.sk" => "tajneHeslo456",
    "test@visiless.cz" => "b2b2026"
];

$error = "";

// 2. Spracovanie odhlásenia
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: b2b.php");
    exit;
}

// 3. Spracovanie prihlásenia
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (array_key_exists($email, $allowed_users) && $allowed_users[$email] === $password) {
        $_SESSION['b2b_logged_in'] = true;
        $_SESSION['b2b_email'] = $email;
    } else {
        $error = "Nesprávný e-mail nebo heslo. Přístup zamítnut.";
    }
}

// 4. Ak používateľ NIE JE prihlásený, ukážeme mu LEN prihlasovací formulár
if (!isset($_SESSION['b2b_logged_in']) || $_SESSION['b2b_logged_in'] !== true) {
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisiLess® B2B | Přihlášení</title>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&family=Work+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0a0b0a 0%, #111411 100%); 
            color: white; font-family: 'Work Sans', sans-serif; 
            display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 20px;
        }
        .login-box { 
            background: rgba(20, 22, 20, 0.9); padding: 40px; border-radius: 12px; 
            border: 1px solid rgba(255, 255, 255, 0.08); text-align: center; max-width: 400px; width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5); backdrop-filter: blur(20px);
        }
        h2 { font-family: 'Black Ops One', cursive; font-size: 1.8rem; margin-bottom: 5px; letter-spacing: 1px; }
        p.sub { color: #8b928b; font-size: 0.9rem; margin-bottom: 25px; }
        input { 
            width: 100%; padding: 15px; margin: 10px 0; border-radius: 8px; 
            border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(0,0,0,0.5); 
            color: white; box-sizing: border-box; font-family: 'Work Sans', sans-serif; font-size: 1rem; transition: 0.3s;
        }
        input:focus { border-color: #4B5320; outline: none; background: #000; }
        button { 
            width: 100%; padding: 15px; background: #4B5320; color: white; border: none; 
            border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 15px; 
            text-transform: uppercase; letter-spacing: 1px; font-size: 1rem; transition: 0.3s;
        }
        button:hover { background: #5d6628; transform: translateY(-2px); }
        .error { background: rgba(255, 82, 82, 0.1); color: #ff5252; padding: 10px; border-radius: 6px; border: 1px solid #ff5252; margin-bottom: 15px; font-size: 0.9em; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="logo_visiless.png" alt="VisiLess" style="max-width: 180px; margin-bottom: 15px;" onerror="this.style.display='none'">
        <h2>B2B PORTÁL</h2>
        <p class="sub">Pro vstup zadejte své partnerské údaje</p>
        
        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Váš e-mail" required>
            <input type="password" name="password" placeholder="Heslo" required>
            <button type="submit" name="login">Přihlásit se</button>
        </form>
    </div>
</body>
</html>
<?php
    exit; // Týmto sa skript zastaví a kód konfigurátora sa pre neprihlásených vôbec nenačíta!
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VisiLess® | B2B Objednávkový Portál</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --brand-green: #4B5320;
            --brand-green-light: #5d6628;
            --bg-dark: #0a0b0a;
            --surface: rgba(20, 22, 20, 0.9);
            --border: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-muted: #8b928b;
            --success: #4caf50;
            --warning: #ffb300;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: linear-gradient(135deg, var(--bg-dark) 0%, #111411 100%); 
            color: var(--text-main); 
            font-family: 'Work Sans', sans-serif; 
            padding: 20px; 
            min-height: 100vh;
        }
        
        .brand-font { font-family: 'Black Ops One', cursive; letter-spacing: 1.5px; text-transform: uppercase; }
        
        /* Hlavička s odhlásením */
        .top-bar { display: flex; justify-content: space-between; align-items: center; max-width: 1550px; margin: 0 auto; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        .logged-user { color: var(--text-muted); font-size: 0.9rem; }
        .logout-btn { color: #ff5252; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding: 8px 12px; border: 1px solid rgba(255,82,82,0.3); border-radius: 6px; transition: 0.2s; }
        .logout-btn:hover { background: #ff5252; color: #fff; border-color: #ff5252; }

        .header { text-align: center; padding: 30px 0 40px; }
        .logo { max-width: 220px; margin-bottom: 20px; }
        .subtitle { color: var(--text-muted); font-size: 1.05rem; font-weight: 300; margin-top: 8px; }

        .container { max-width: 1550px; margin: 0 auto; display: grid; grid-template-columns: 1fr 450px; gap: 40px; }

        /* B2B Katalóg */
        .catalog { display: flex; flex-direction: column; gap: 25px; }
        .product-group { 
            background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); 
            border-radius: 12px; padding: 30px; transition: all 0.3s ease; 
        }
        .product-group:hover { border-color: rgba(75, 83, 32, 0.5); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        
        .group-header { display: flex; align-items: center; gap: 25px; margin-bottom: 25px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
        
        .group-img { 
            width: 100px; height: 100px; background-color: #ffffff; border-radius: 8px; 
            object-fit: contain; padding: 10px; box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }
        
        .group-title { font-size: 1.5rem; font-weight: 600; color: #fff; letter-spacing: 0.5px; }
        .group-moc { font-size: 1rem; color: var(--brand-green); font-weight: 500; margin-top: 6px; }

        /* Tabuľka variantov */
        .v-row { display: grid; grid-template-columns: 1.2fr 1.5fr 1.5fr; gap: 20px; align-items: center; padding: 15px 0; border-bottom: 1px dashed var(--border); }
        .v-row:last-child { border-bottom: none; padding-bottom: 0; }
        .v-code { font-family: 'Courier New', monospace; color: #fff; font-size: 1.1rem; font-weight: 600; letter-spacing: 0.5px; }
        .v-spec { display: flex; flex-direction: column; gap: 6px; }
        .v-spec-title { color: var(--text-muted); font-size: 0.95rem; }
        .v-tier-info { font-size: 0.85rem; font-weight: 500; min-height: 20px; padding: 4px 8px; background: rgba(0,0,0,0.2); border-radius: 4px; width: fit-content; line-height: 1.4; }
        
        .qty-controls { display: flex; align-items: center; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
        .quick-btn { background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border); color: var(--text-main); padding: 10px 14px; border-radius: 6px; cursor: pointer; font-size: 0.95rem; font-weight: 600; transition: all 0.2s; }
        .quick-btn:hover { background: var(--brand-green); border-color: var(--brand-green); color: #fff; }
        .quick-btn.clear-btn { color: #ff5252; border-color: transparent; background: transparent; font-size: 1.2rem; padding: 8px; }
        .quick-btn.clear-btn:hover { color: #ff1744; transform: scale(1.1); }
        input[type="number"] { background: #000; border: 1px solid var(--brand-green); color: white; padding: 12px; border-radius: 6px; width: 90px; text-align: center; font-size: 1.1rem; font-weight: 600; box-shadow: 0 0 10px rgba(75, 83, 32, 0.2); }
        input[type="number"]:focus { outline: none; border-color: #fff; }

        /* Pravý Panel - Košík */
        .sidebar { background: var(--surface); border: 1px solid var(--border); padding: 30px; border-radius: 12px; height: fit-content; position: sticky; top: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .cart-header { font-family: 'Black Ops One', cursive; font-size: 1.6rem; color: #fff; margin-bottom: 25px; border-bottom: 2px solid var(--brand-green); padding-bottom: 15px; }
        
        .cart-list-preview { margin-bottom: 25px; max-height: 400px; overflow-y: auto; padding-right: 10px; }
        .cart-list-preview::-webkit-scrollbar { width: 6px; }
        .cart-list-preview::-webkit-scrollbar-thumb { background: var(--brand-green); border-radius: 3px; }
        
        .cart-item-card { background: rgba(0,0,0,0.4); border: 1px solid var(--border); border-radius: 8px; padding: 15px; margin-bottom: 12px; }
        .cart-item-head { display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 10px; color: #fff; font-size: 1.05rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; }
        .cart-item-math { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85rem; color: var(--text-muted); }
        .cart-item-math div strong { color: #fff; font-weight: 500; }
        .cart-item-profit { grid-column: 1 / -1; margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.05); color: var(--success); font-weight: 600; font-size: 0.95rem; }

        .summary-box { background: #000; padding: 25px; border-radius: 8px; border: 1px solid var(--border); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 1rem; color: var(--text-muted); }
        .summary-row.profit { color: var(--success); font-weight: 600; font-size: 1.1rem; }
        .summary-row.total { font-weight: 600; font-size: 1.4rem; color: white; border-top: 2px solid var(--border); padding-top: 20px; margin-top: 15px; margin-bottom: 0; }

        .btn-custom { background-color: var(--brand-green); color: #ffffff; border: none; padding: 18px 20px; text-transform: uppercase; font-family: 'Work Sans', sans-serif; font-weight: 600; font-size: 1.1rem; letter-spacing: 1px; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; margin-top: 25px; box-shadow: 0 4px 15px rgba(75, 83, 32, 0.4); }
        .btn-custom:hover:not(:disabled) { background-color: var(--brand-green-light); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(75, 83, 32, 0.6); }
        .btn-custom:disabled { background: #2a2a2a; color: #555; box-shadow: none; cursor: not-allowed; transform: none; }

        /* Skryjeme email vo formulári, lebo e-mail už vieme z PHP Session */
        .hidden-email { display: none; }

        @media (max-width: 1200px) {
            .container { grid-template-columns: 1fr; }
            .sidebar { position: static; order: 2; margin-top: 20px; }
            .catalog { order: 1; }
            .v-row { grid-template-columns: 1fr; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 10px; border: 1px solid var(--border); }
            .qty-controls { justify-content: space-between; margin-top: 15px; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 8px; }
            .quick-btn { flex: 1; text-align: center; padding: 12px 0; }
            input[type="number"] { width: 100%; margin-top: 10px; padding: 15px; }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="logged-user"><i class="fas fa-user-circle"></i> Přihlášený partner: <strong><?php echo htmlspecialchars($_SESSION['b2b_email']); ?></strong></div>
        <a href="b2b.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Odhlásit se</a>
    </div>

    <div class="header">
        <img src="logo_visiless.png" alt="VisiLess" class="logo" onerror="this.style.display='none'">
        <h2 class="brand-font" style="color: #fff; font-size: 2.2rem;">B2B <span style="color: var(--brand-green)">PORTÁL</span></h2>
        <p class="subtitle">Profesionální velkoobchodní systém. Obchodní marže se počítá pro každý model individuálně.</p>
    </div>

    <div class="container">
        <div class="catalog" id="catalog-container"></div>

        <div class="sidebar">
            <div class="cart-header">Souhrn Objednávky</div>
            
            <div class="cart-list-preview" id="cart-list-preview">
                <div style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Košík je zatím prázdný.</div>
            </div>

            <div class="summary-box">
                <div class="summary-row"><span>Celkem MOC (bez DPH):</span><span id="sum-moc">0,00 €</span></div>
                <div class="summary-row"><span>Celkem Nákup (VOC bez DPH):</span><span id="sum-voc" style="color: #fff;">0,00 €</span></div>
                <div class="summary-row profit"><span>Váš čistý zisk (Marže):</span><span id="sum-profit">0,00 €</span></div>
                <div class="summary-row" style="margin-top: 15px; border-top: 1px solid var(--border); padding-top: 15px;"><span>DPH (21 %):</span><span id="sum-tax">0,00 €</span></div>
                <div class="summary-row total"><span>K úhradě (s DPH):</span><span id="sum-final">0,00 €</span></div>
            </div>

            <form action="https://formspree.io/f/mvzbnzbv" method="POST" id="b2b-form">
                <input type="email" name="email" class="hidden-email" value="<?php echo htmlspecialchars($_SESSION['b2b_email']); ?>" required>
                <textarea name="message" id="hidden-message" style="display:none"></textarea>
                <button type="submit" class="btn-custom" id="submit-order" disabled>Odeslat závaznou objednávku</button>
            </form>
        </div>
    </div>

    <script>
        const formatEur = (number) => new Intl.NumberFormat('cs-CZ', { style: 'currency', currency: 'EUR' }).format(number);

        const catalogData = [
            {
                name: 'LiveBait 1Hook PRO', moc: 179, img: 'item/hook1.png',
                variants: [
                    { code: 'LB-1H-PRO-4225', spec: '4,2 kg | 25 cm' }, { code: 'LB-1H-PRO-6525', spec: '6,5 kg | 25 cm' }, { code: 'LB-1H-PRO-9625', spec: '9,6 kg | 25 cm' },
                    { code: 'LB-1H-PRO-4240', spec: '4,2 kg | 40 cm' }, { code: 'LB-1H-PRO-6540', spec: '6,5 kg | 40 cm' }, { code: 'LB-1H-PRO-9640', spec: '9,6 kg | 40 cm' }
                ]
            },
            {
                name: 'LiveBait 1+1 Hook PRO', moc: 179, img: 'item/hook11.png',
                variants: [
                    { code: 'LB-11H-PRO-4225', spec: '4,2 kg | 25 cm' }, { code: 'LB-11H-PRO-6525', spec: '6,5 kg | 25 cm' }, { code: 'LB-11H-PRO-9625', spec: '9,6 kg | 25 cm' },
                    { code: 'LB-11H-PRO-4240', spec: '4,2 kg | 40 cm' }, { code: 'LB-11H-PRO-6540', spec: '6,5 kg | 40 cm' }, { code: 'LB-11H-PRO-9640', spec: '9,6 kg | 40 cm' }
                ]
            },
            {
                name: 'LiveBait 1+3 Hook PRO', moc: 179, img: 'item/hook13.png',
                variants: [
                    { code: 'LB-13H-PRO-4225', spec: '4,2 kg | 25 cm' }, { code: 'LB-13H-PRO-6525', spec: '6,5 kg | 25 cm' }, { code: 'LB-13H-PRO-9625', spec: '9,6 kg | 25 cm' },
                    { code: 'LB-13H-PRO-4240', spec: '4,2 kg | 40 cm' }, { code: 'LB-13H-PRO-6540', spec: '6,5 kg | 40 cm' }, { code: 'LB-13H-PRO-9640', spec: '9,6 kg | 40 cm' }
                ]
            },
            {
                name: 'LiveBait TrebleHook PRO', moc: 159, img: 'item/hook3.png',
                variants: [
                    { code: 'LB-3H-PRO-4225', spec: '4,2 kg | 25 cm' }, { code: 'LB-3H-PRO-6525', spec: '6,5 kg | 25 cm' }, { code: 'LB-3H-PRO-9625', spec: '9,6 kg | 25 cm' },
                    { code: 'LB-3H-PRO-4240', spec: '4,2 kg | 40 cm' }, { code: 'LB-3H-PRO-6540', spec: '6,5 kg | 40 cm' }, { code: 'LB-3H-PRO-9640', spec: '9,6 kg | 40 cm' }
                ]
            },
            {
                name: 'Spinning PRO Series', moc: 199, img: 'item/spinning.png',
                variants: [
                    { code: 'S-PRO-4225', spec: '4,2 kg | 25 cm' }, { code: 'S-PRO-6525', spec: '6,5 kg | 25 cm' }, { code: 'S-PRO-9625', spec: '9,6 kg | 25 cm' },
                    { code: 'S-PRO-4240', spec: '4,2 kg | 40 cm' }, { code: 'S-PRO-6540', spec: '6,5 kg | 40 cm' }, { code: 'S-PRO-9640', spec: '9,6 kg | 40 cm' }
                ]
            },
            {
                name: 'GHOSTBAIT ELITE', moc: 190, img: 'item/ghost.png',
                variants: [
                    { code: 'GHOSTBAIT-6525-3', spec: '6,5 kg | 25 cm (zátěž 3g)' }
                ]
            }
        ];

        const b2bTiers = [
            { minQty: 100, marginPct: 0.50 }, { minQty: 50,  marginPct: 0.45 },
            { minQty: 30,  marginPct: 0.40 }, { minQty: 10,  marginPct: 0.35 },
            { minQty: 1,   marginPct: 0.30 }, { minQty: 0,   marginPct: 0.00 }
        ];

        const VAT_RATE = 1.21;
        let orderItems = {}; 

        const container = document.getElementById('catalog-container');
        catalogData.forEach((group, gIndex) => {
            let html = `
            <div class="product-group">
                <div class="group-header">
                    <img src="${group.img}" alt="${group.name}" class="group-img" onerror="this.style.display='none'">
                    <div>
                        <div class="group-title">${group.name}</div>
                        <div class="group-moc">Základní MOC: ${formatEur(group.moc)} s DPH</div>
                    </div>
                </div>
                <div class="variants-table">`;
            
            group.variants.forEach((v, vIndex) => {
                const inputId = `input-${gIndex}-${vIndex}`;
                const infoId = `info-${gIndex}-${vIndex}`;
                html += `
                    <div class="v-row">
                        <div class="v-code">${v.code}</div>
                        <div class="v-spec">
                            <span class="v-spec-title">${v.spec}</span>
                            <div class="v-tier-info" id="${infoId}">Zadejte kusy pro výpočet marže</div>
                        </div>
                        <div class="qty-controls">
                            <button class="quick-btn clear-btn" onclick="addQty(${gIndex}, ${vIndex}, 'clear')" title="Odstranit"><i class="fas fa-trash-alt"></i></button>
                            <button class="quick-btn" onclick="addQty(${gIndex}, ${vIndex}, 1)">+1</button>
                            <button class="quick-btn" onclick="addQty(${gIndex}, ${vIndex}, 10)">+10</button>
                            <button class="quick-btn" onclick="addQty(${gIndex}, ${vIndex}, 30)">+30</button>
                            <button class="quick-btn" onclick="addQty(${gIndex}, ${vIndex}, 50)">+50</button>
                            <input type="number" min="0" placeholder="0" id="${inputId}" data-g="${gIndex}" data-v="${vIndex}" oninput="handleInput(this)">
                        </div>
                    </div>`;
            });
            html += `</div></div>`;
            container.innerHTML += html;
        });

        window.addQty = function(g, v, action) {
            const input = document.getElementById(`input-${g}-${v}`);
            let currentQty = parseInt(input.value) || 0;
            if (action === 'clear') input.value = '';
            else input.value = currentQty + action;
            handleInput(input);
        };

        window.handleInput = function(input) {
            const g = input.getAttribute('data-g');
            const v = input.getAttribute('data-v');
            const qty = parseInt(input.value) || 0;
            const group = catalogData[g];
            const infoSpan = document.getElementById(`info-${g}-${v}`);
            
            if (qty === 0) {
                infoSpan.innerHTML = "Zadejte kusy pro výpočet marže";
                infoSpan.style.color = "var(--text-muted)";
                infoSpan.style.background = "rgba(0,0,0,0.2)";
            } else {
                const itemTier = b2bTiers.find(t => qty >= t.minQty);
                const nextTier = [...b2bTiers].reverse().find(t => t.minQty > qty);
                const moc_bez_DPH = group.moc / VAT_RATE;
                const profit_per_piece = moc_bez_DPH * itemTier.marginPct;
                const voc_bez_DPH = moc_bez_DPH - profit_per_piece;

                infoSpan.style.background = "rgba(0,0,0,0.4)";
                let text = `<span style="color:#fff">MOC (bez DPH): ${formatEur(moc_bez_DPH)} | VOC: ${formatEur(voc_bez_DPH)}</span><br>`;
                
                if (nextTier) {
                    text += `<span style="color:var(--success)">Zisk: ${formatEur(profit_per_piece)}/ks (${itemTier.marginPct * 100}%)</span> <span style="color:var(--warning); font-size: 0.8rem;">(Chybí ${nextTier.minQty - qty} ks na ${nextTier.marginPct * 100}%)</span>`;
                } else {
                    text += `<span style="color:var(--success)">Zisk: ${formatEur(profit_per_piece)}/ks (${itemTier.marginPct * 100}% - MAX)</span>`;
                }
                infoSpan.innerHTML = text;
            }
            
            const key = `${g}-${v}`;
            if (qty > 0) orderItems[key] = { qty, group: catalogData[g], variant: catalogData[g].variants[v] };
            else delete orderItems[key];
            calculateCart();
        };

        function calculateCart() {
            let sumMoc = 0; let sumVoc = 0; let sumProfit = 0;
            let emailText = "B2B OBJEDNÁVKA - DETAILNÍ ROZPIS\\n=================================\\n";
            emailText += "Partner: <?php echo htmlspecialchars($_SESSION['b2b_email'] ?? ''); ?>\\n\\n";
            let previewHTML = "";

            Object.values(orderItems).forEach(item => {
                const moc_bez_DPH_ks = item.group.moc / VAT_RATE;
                const itemTier = b2bTiers.find(t => item.qty >= t.minQty);
                const marginDec = itemTier.marginPct;
                
                const profit_ks = moc_bez_DPH_ks * marginDec; 
                const voc_bez_DPH_ks = moc_bez_DPH_ks - profit_ks;
                
                const lineMoc = moc_bez_DPH_ks * item.qty;
                const lineVoc = voc_bez_DPH_ks * item.qty;
                const lineProfit = profit_ks * item.qty;

                sumMoc += lineMoc; sumVoc += lineVoc; sumProfit += lineProfit;
                const marginDisplay = `${marginDec * 100}%`;

                emailText += `[ ${item.variant.code} ] - ${item.qty} ks\\n`;
                emailText += `   MOC (bez DPH): ${formatEur(moc_bez_DPH_ks)} / ks\\n`;
                emailText += `   Vaše nákupní cena VOC: ${formatEur(voc_bez_DPH_ks)} / ks\\n`;
                emailText += `   Čistý zisk za položku: ${formatEur(lineProfit)} (Marže: ${marginDisplay})\\n\\n`;

                previewHTML += `
                    <div class="cart-item-card">
                        <div class="cart-item-head"><span>${item.qty}x ${item.variant.code}</span><span style="color:var(--brand-green);">${marginDisplay}</span></div>
                        <div class="cart-item-math">
                            <div>MOC (bez DPH):<br><strong>${formatEur(moc_bez_DPH_ks)}/ks</strong></div>
                            <div>Nákup (bez DPH):<br><strong>${formatEur(voc_bez_DPH_ks)}/ks</strong></div>
                            <div class="cart-item-profit">Váš zisk z položky: ${formatEur(lineProfit)}</div>
                        </div>
                    </div>`;
            });

            const totalTax = sumVoc * (VAT_RATE - 1);
            const totalFinal = sumVoc * VAT_RATE;

            if (Object.keys(orderItems).length === 0) {
                previewHTML = `<div style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Košík je zatím prázdný.</div>`;
            }

            document.getElementById('sum-moc').innerText = formatEur(sumMoc);
            document.getElementById('sum-voc').innerText = formatEur(sumVoc);
            document.getElementById('sum-profit').innerText = formatEur(sumProfit);
            document.getElementById('sum-tax').innerText = formatEur(totalTax);
            document.getElementById('sum-final').innerText = formatEur(totalFinal);
            document.getElementById('cart-list-preview').innerHTML = previewHTML;

            const btn = document.getElementById('submit-order');
            btn.disabled = Object.keys(orderItems).length === 0;

            emailText += "=================================\\n";
            emailText += `CELKEM MOC (bez DPH): ${formatEur(sumMoc)}\\n`;
            emailText += `CELKEM VOC (bez DPH): ${formatEur(sumVoc)}\\n`;
            emailText += `VÁŠ ČISTÝ ZISK:       ${formatEur(sumProfit)}\\n`;
            emailText += `DPH (21%):            ${formatEur(totalTax)}\\n`;
            emailText += `CELKEM K ÚHRADĚ s DPH:${formatEur(totalFinal)}`;
            document.getElementById('hidden-message').value = emailText;
        }
    </script>
</body>
</html>