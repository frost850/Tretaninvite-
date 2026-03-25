/* ══════════════════════════════════════════════════════════════
   SHARED WEDDING MUSIC PLAYER  ·  music-player.js
   Supports:
     · YouTube (audio-only, 144p, hidden iframe, loop)
     · Direct audio (MP3 / any Audio-compatible URL)
   Config via:  window.musicData = { url, ytId, btnId }
   Autoplay on first user tap/click anywhere on the page.
   Toggle button shows ♪ / ♫ and .playing class.
══════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var _d       = window.musicData || {};
    var _ytId    = (_d.ytId  || '').trim();
    var _url     = (_d.url   || '').trim();
    var _btnId   = _d.btnId  || 'wp-music-btn';

    if (!_ytId && !_url) return; // nothing configured

    /* ── Show button immediately ──────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () { _showBtn(); });
    if (document.readyState !== 'loading') _showBtn();

    var _started   = false;
    var _playing   = false;
    var _audio     = null;
    var _ytPlayer  = null;

    /* ── helpers ──────────────────────────────────────────── */
    function _btn() { return document.getElementById(_btnId); }

    function _showBtn() {
        var b = _btn();
        if (b) b.style.display = 'flex';
    }

    function _setState(on) {
        _playing = on;
        var b = _btn();
        if (!b) return;
        if (on) {
            b.classList.add('playing');
            b.title     = 'Pause Musik';
            b.innerHTML = '♫';
        } else {
            b.classList.remove('playing');
            b.title     = 'Putar Musik';
            b.innerHTML = '♪';
        }
    }

    /* ── YouTube mode ─────────────────────────────────────── */
    if (_ytId) {
        function _initYT() {
            _ytPlayer = new YT.Player('wp-yt-player', {
                height:  '1',
                width:   '1',
                videoId: _ytId,
                playerVars: {
                    autoplay:       0,
                    controls:       0,
                    disablekb:      1,
                    fs:             0,
                    iv_load_policy: 3,
                    modestbranding: 1,
                    rel:            0,
                    loop:           1,
                    playlist:       _ytId,   // required for loop
                    vq:             'tiny',  // 144p — low bandwidth
                },
                events: {
                    onReady: function (e) {
                        e.target.setVolume(35);
                        e.target.setPlaybackQuality('tiny');
                        // auto-play if user already clicked before player was ready
                        if (_started) {
                            e.target.setPlaybackQuality('tiny');
                            e.target.playVideo();
                        }
                    },
                    onStateChange: function (e) {
                        if (e.data === YT.PlayerState.PLAYING) _setState(true);
                        if (e.data === YT.PlayerState.PAUSED)  _setState(false);
                        if (e.data === YT.PlayerState.ENDED)   _setState(false);
                    }
                }
            });
        }
        // Handle race condition: if YT API already fired before this script ran
        if (window.YT && window.YT.Player) {
            _initYT();
        } else {
            var _prevReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function () {
                if (typeof _prevReady === 'function') _prevReady();
                _initYT();
            };
        }
    }

    /* ── Direct-audio mode ────────────────────────────────── */
    function _initAudio() {
        if (_audio) return;
        _audio        = new Audio(_url);
        _audio.loop   = true;
        _audio.volume = 0.35;
        _audio.addEventListener('play',  function () { _setState(true);  });
        _audio.addEventListener('pause', function () { _setState(false); });
        _audio.addEventListener('ended', function () { _setState(false); });
        _showBtn();
    }

    /* ── Start (first interaction) ────────────────────────── */
    function _start() {
        if (_started) return;
        _started = true;
        if (_ytId) {
            if (_ytPlayer && typeof _ytPlayer.playVideo === 'function') {
                _ytPlayer.setPlaybackQuality('tiny');
                _ytPlayer.playVideo();
            }
            // else: onReady handler above will auto-play when player is ready
        } else {
            _initAudio();
            _audio.play().catch(function () {});
        }
    }

    /* ── Public toggle ────────────────────────────────────── */
    window.toggleMusic = function () {
        if (_ytId) {
            if (!_ytPlayer) return;
            if (_playing) {
                _ytPlayer.pauseVideo();
            } else {
                _ytPlayer.setPlaybackQuality('tiny');
                _ytPlayer.playVideo();
            }
        } else {
            if (!_audio) { _initAudio(); _audio.play().catch(function(){}); return; }
            if (_playing) {
                _audio.pause();
            } else {
                _audio.play().catch(function(){});
            }
        }
    };

    /* ── Auto-play on first interaction ──────────────────── */
    document.addEventListener('click', function _first() {
        document.removeEventListener('click', _first);
        _start();
    });

}());
