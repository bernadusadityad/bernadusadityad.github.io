<?php
$kelurahan = [
		"bidang_guntur" => "#red",
		"bidang_karet"=>"blue",
		"bidang_karet_kuningan">="yellow",
		"bidang_karet_semanggi">="green",
		"bidang_kuningan_timur_REVISI">="blue",
		"bidang_menteng_atas">="yellow",
		"bidang_pasar_manggis">="red",
		"bidang_setiabudi">="yellow"
		];
?>  

<!DOCTYPE html>
<html>
<head>
    <title>Belajar Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <!-- Load Library Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <link rel="stylesheet" href="leaflet-panel-layers-master\leaflet-panel-layers-master\src\leaflet-panel-layers.css" />

    <!-- Load Plugin leaflet.ajax -->
    <script src="leaflet.ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/stefanocudini/leaflet-panel-layers/src/leaflet-panel-layers.js"></script>

    <style>
        #map {
        height: 100vh;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <script type="text/javascript">
        // Inisialisasi Peta
        var map = L.map('map').setView([-6.2322614,106.8183028], 17);

        // Tambahkan Layer Peta (Basemap)
        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });
        map.addLayer(Esri_WorldImagery);

        // Tambahkan Marker
        var marker = L.marker([-6.229747, 106.81818]).addTo(map);

        // Interaksi Marker
        marker.bindPopup("<b>Ini rumahnya pacarnya</b><br>Sayang.").openPopup();

        // Popup Statis
        var popup = L.popup()
            .setLatLng([-6.2322614,106.8183028])
            .setContent("Kamu bisa mengetahui koordinat di peta ini")
            .openOn(map);

        // Event Click di Peta
        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("Kamu memilih lokasi berkoordinat: " + e.latlng.toString())
                .openOn(map);
        }
        map.on('click', onMapClick);


        //Fungsi PHP
        var kelurahan = <?php echo json_encode($kelurahan); ?>;
        
        
        // Fungsi Popup GeoJSON
        function popUp(feature, layer) {
            var out = [];
            if (feature.properties) {
                for (var key in feature.properties) {
                    out.push(key + ": " + feature.properties[key]);
                }
                layer.bindPopup(out.join("<br />"));
            }
        }

        // Tambahkan GeoJSON Menggunakan leaflet.ajax
        function popUp(feature, layer) {
        var out = [];
        
        if (feature.properties) {
            // Cek apakah atribut 'kota', 'kecamatan', 'kelurahan' tersedia
            if (feature.properties.kota || feature.properties.kecamatan || feature.properties.kelurahan) {
                out.push("<b>Informasi Lokasi:</b>");
                if (feature.properties.kota) out.push("Kota: " + feature.properties.kota);
                if (feature.properties.kecamatan) out.push("Kecamatan: " + feature.properties.kecamatan);
                if (feature.properties.kelurahan) out.push("Kelurahan: " + feature.properties.kelurahan);
            } else {
                out.push("<b>Informasi Lokasi:</b>");
                if (feature.properties.WADMKD) out.push("Kelurahan: " + feature.properties.WADMKD);

            }

            // Tambahkan popup ke layer
            layer.bindPopup(out.join("<br />"));
             }
         }

        // Style untuk GeoJSON 1
        var myStyle = {
            "color": "yellow",
            "weight": 2,
            "opacity": 0.3
        };

        // Style untuk GeoJSON 2
        var myStyle2 = {
            "color": "red",
            "weight": 5,
        };

        // Tambahkan GeoJSON pertama
        var jsonLayer1 = new L.GeoJSON.AJAX("data_geojson\bidang_kuningan_timur_REVISI.geojson", {
            onEachFeature: popUp,
            style: myStyle
        })

        // Tambahkan GeoJSON kedua
        var jsonLayer2 = new L.GeoJSON.AJAX("data_geojson\batas_kuningan_timur.geojson", {
            onEachFeature: popUp,
            style: myStyle2
        })
            


    //Program plugin legenda


    function iconByName(name) {
        return '<i class="icon icon-'+name+'"></i>';
    }

    function featureToMarker(feature, latlng) {
        return L.marker(latlng, {
            icon: L.divIcon({
                className: 'marker-'+feature.properties.amenity,
                html: iconByName(feature.properties.amenity),
                iconUrl: '../images/markers/'+feature.properties.amenity+'.png',
                iconSize: [25, 41],
                iconAnchor: [20, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        });
    }

    var baseLayers = [
        {
            name: "OpenStreetMap",
            layer: Esri_WorldImagery
        },
        {
            name: "OpenCycleMap",
            layer: L.tileLayer('https://{s}.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png')
        },
        {
            name: "Outdoors",
            layer: L.tileLayer('https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png')
        }
    ];

    var overLayers = [
        {
            name: "Bidang Kelurahan Kuningan Timur",
            icon: iconByName('bar'),
            layer: new L.GeoJSON.AJAX(["bidang_kuningan_timur_REVISI.geojson"],{onEachFeature:popUp,style: myStyle,pointToLayer: featureToMarker }).addTo(map)
                    

        },
        {
            name: "Batas Kelurahan Kuningan Timur",
            icon: iconByName('drinking_water'),
            layer:new L.GeoJSON.AJAX(["batas_kuningan_timur.geojson"],{onEachFeature:popUp,style: myStyle2,pointToLayer: featureToMarker }).addTo(map)

        }
        
    ];

    var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers);

    map.addControl(panelLayers); 
      
    </script>
</body>
</html>
