/**
 * gc-threejs.js
 * Greeting Card — Three.js 3D Particle Background
 *
 * Dependensi: THREE (muat three.min.js sebelum file ini)
 * Elemen yang dibutuhkan: #gc-three-canvas, #gc-hero
 *
 * Ekspor: window.GcThreeJS = { init }
 * Panggil GcThreeJS.init() setelah kartu dibuka.
 *
 * Konfigurasi warna partikel dapat diganti lewat:
 *   window.GC_THREE_PALETTE = ['#hex1', '#hex2', ...]  (sebelum init)
 */
(function () {
    'use strict';

    function init() {
        const canvas = document.getElementById('gc-three-canvas');
        const heroEl = document.getElementById('gc-hero');
        if (!canvas || !heroEl || typeof THREE === 'undefined') return;

        /* ── Renderer ── */
        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        /* ── Scene & Camera ── */
        const scene  = new THREE.Scene();
        let   w = heroEl.clientWidth, h = heroEl.clientHeight;
        const camera = new THREE.PerspectiveCamera(60, w / h, 0.1, 100);
        camera.position.z = 6;

        /* ── Particle System ── */
        const COUNT   = 280;
        const pos     = new Float32Array(COUNT * 3);
        const col     = new Float32Array(COUNT * 3);
        const rawPal  = (window.GC_THREE_PALETTE && window.GC_THREE_PALETTE.length)
            ? window.GC_THREE_PALETTE
            : ['#8338ec', '#ff6b9d', '#ffbe0b', '#ffffff', '#c45abf', '#f9a8d4'];
        const palette = rawPal.map(h => new THREE.Color(h));

        for (let i = 0; i < COUNT; i++) {
            pos[i * 3]     = (Math.random() - 0.5) * 22;
            pos[i * 3 + 1] = (Math.random() - 0.5) * 18;
            pos[i * 3 + 2] = (Math.random() - 0.5) * 8;
            const c = palette[i % palette.length];
            col[i * 3] = c.r; col[i * 3 + 1] = c.g; col[i * 3 + 2] = c.b;
        }
        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        geo.setAttribute('color',    new THREE.BufferAttribute(col, 3));
        const mat = new THREE.PointsMaterial({
            size: 0.18, vertexColors: true, transparent: true, opacity: 0.85,
        });
        const particles = new THREE.Points(geo, mat);
        scene.add(particles);

        /* ── Decorative Tori ── */
        const addTorus = (r, tube, color, opacity, rx) => {
            const m = new THREE.Mesh(
                new THREE.TorusGeometry(r, tube, 6, 60),
                new THREE.MeshBasicMaterial({ color, transparent: true, opacity })
            );
            m.rotation.x = rx;
            scene.add(m);
            return m;
        };
        const t1 = addTorus(2.5, 0.03, 0xc084fc, 0.25,  0.4);
        const t2 = addTorus(3.5, 0.02, 0xff6b9d, 0.18, -0.6);

        /* ── Mouse tracking ── */
        let mx = 0, my = 0, tmx = 0, tmy = 0;
        document.addEventListener('mousemove', e => {
            mx = (e.clientX / window.innerWidth  - 0.5) * 2;
            my = -(e.clientY / window.innerHeight - 0.5) * 2;
        });

        /* ── Resize ── */
        const onResize = () => {
            w = heroEl.clientWidth; h = heroEl.clientHeight;
            renderer.setSize(w, h);
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
        };
        window.addEventListener('resize', onResize);
        onResize();

        /* ── Animation loop ── */
        let t = 0;
        (function animate() {
            requestAnimationFrame(animate);
            t += 0.008;
            tmx += (mx - tmx) * 0.025;
            tmy += (my - tmy) * 0.025;
            particles.rotation.y = t * 0.12 + tmx * 0.15;
            particles.rotation.x = t * 0.06 + tmy * 0.10;
            t1.rotation.z = t * 0.18;
            t2.rotation.z = -t * 0.12;
            camera.position.x += (tmx * 0.4  - camera.position.x) * 0.03;
            camera.position.y += (tmy * 0.25 - camera.position.y) * 0.03;
            camera.lookAt(scene.position);
            /* Hentikan render saat hero tidak terlihat untuk hemat GPU */
            if (heroEl.getBoundingClientRect().bottom > -100) {
                renderer.render(scene, camera);
            }
        })();
    }

    window.GcThreeJS = { init };
})();
