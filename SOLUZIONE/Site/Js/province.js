// array associativo delle province italiane
let province = {
    "Abruzzo": [
        "AQ-(l'Acquila)",
        "CH-(Chiasso)",
        "PE-(Pescara)",
        "TE-(Teramo)"
    ],
    "Basilicata": [
        "MT-(Matera)",
        "PZ-(Potenza)"
    ],
    "Calabria": [
        "CS-(Cosenza)",
        "CZ-(Catanzaro)",
        "KR-(Crotone)",
        "RC-(Reggio Calabria)",
        "VV-(Vibo Valentia)"
    ],
    "Campania": [
        "AV-(Avellino)",
        "BN-(Benevento)",
        "CE-(Caserta)",
        "NA-(Napoli)",
        "SA-(Salerno)"
    ],
    "Emilia-Romagna": [
        "BO-(Bologna)",
        "FE-(Ferrara)",
        "FC-(Forlì Cesena)",
        "MO-(Modena)",
        "PR-(Parma)",
        "PC-(Piacenza)",
        "RA-(Ravenna)",
        "RN-(Rimini)"
    ],
    "Friuli-Venezia Giulia": [
        "GO-(Gorizia)",
        "PN-(Pordenone)",
        "TS-(Trieste)",
        "UD-(Udine)"
    ],
    "Lazio": [
        "FR-(Frosinone)",
        "LT-(Latina)",
        "RI-(Rieti)",
        "RM-(Roma)",
        "VT-(Viterbo)"
    ],
    "Liguria": [
        "GE-(Genova)",
        "IM-(Imperia)",
        "SP-(La Spezia)",
        "SV-(Savona)"
    ],
    "Lombardia": [
		"BG-(Bergamo)",
		"BS-(Brescia)",
		"CO-(Como)",
		"CR-(Cremona)",
		"LC-(Lecco)",
		"LO-(Lodi)",
		"MN-(Mantova)",
		"MI-(Milano)",
		"MB-(Monza Brianza)",
		"PV-(Pavia)",
		"SO-(Sondrio)",
		"VA-(Varese)"
	],
    "Marche": [
		"AN-(Ancona)",
		"AP-(Ascoli Piceno)",
		"FM-(Fermo)",
		"MC-(Macerata)",
		"PU-(Pesaro Urbino)"
	],
    "Molise": [
		"CB-(Campobasso)",
		"IS-(Isernia)"
	],
    "Piemonte": [
		"AL-(Alessandria)",
		"AT-(Asti)",
		"BI-(Biella)",
		"CN-(Cuneo)",
		"NO-(Novara)",
		"TO-(Torino)",
		"VC-(Vercelli)"
	],
    "Puglia": [
		"BA-(Bari)",
		"BT-(Barletta Andria Trani)",
		"BR-(Brindisi)",
		"FG-(Foggia)",
		"LE-(Lecce)",
		"TA-(Taranto)"
	],
    "Sardegna": [
		"CA-(Cagliari)",
		"CI-(Carbonia-Iglesias)",
		"VS-(Medio Campidano)",
		"NU-(Nuoro)",
		"OG-(Olgiastra)",
		"OT-(Olbia-Tempio)",
		"OR-(Oristano)",
		"SS-(Sassari)"
	],
    "Sicilia": [
		"AG-(Agrigento)",
		"CL-(Caltanissetta)",
		"CT-(Catania)",
		"EN-(Enna)",
		"ME-(Messina)",
		"PA-(Palermo)",
		"RG-(Ragusa)",
		"SR-(Siracusa)",
		"TP-(Trapani)"
	],
    "Toscana": [
		"AR-(Arezzo)",
		"FI-(Firenze)",
		"GR-(Grosseto)",
		"LI-(Livorno)",
		"LU-(Lucca)",
		"MS-(Massa Carrara)",
		"PI-(Pisa)",
		"PT-(Pistoia)",
		"PO-(Prato)",
		"SI-(Siena)"
	],
    "Trentino-Alto Adige": [
		"BZ-(Bolzano)",
		"TN-(Trento)"
	],
    "Umbria": [
		"PG-(Perugia)",
		"TR-(Terni)"
	],
    "Valle d'Aosta": [
		"AO-(Aosta)"
	],
    "Veneto": [
		"BL-(Belluno)",
		"PD-(Padova)",
		"RO-(Rovigo)",
		"TV-(Treviso)",
		"VE-(Venezia)",
		"VR-(Verona)",
		"VI-(Vicenza)"
    ]
};

function getProvince(regione) {
    return province[regione];
}

function getRegioni() {
    return Object.keys(province);
}