/**
 * Hero Slider Plugin — Visual Effects Engine
 *
 * @copyright 2025 Mahmoud Mahsob / Marslia (https://mrslia.com)
 * @author    Mahmoud Mahsob <https://mrslia.com>
 */
(function($) {
    'use strict';

    function rand(a, b) { return Math.random() * (b - a) + a; }

    var REPEL_RADIUS = 120;
    var REPEL_FORCE  = 3.5;

    function applyRepel(p, scene) {
        if (scene.mx < 0 || scene.my < 0) return;
        var dx = p.x - scene.mx, dy = p.y - scene.my;
        var dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < REPEL_RADIUS && dist > 0) {
            var force = (1 - dist / REPEL_RADIUS) * REPEL_FORCE;
            p.x += (dx / dist) * force;
            p.y += (dy / dist) * force;
        }
    }

    var effects = {};

    // ─── 1. Plexus: Particles + Light Beams (Industrial / Tech) ───
    effects.plexus = function(canvas, ctx, scene) {
        var particles = [], beams = [];

        function build() {
            var scale = (window.__hspPerf && window.__hspPerf.scale) || 1;
            var n = Math.max(10, Math.round((scene.w < 768 ? 16 : 24) * scale)), bn = scene.w < 768 ? 2 : 3;
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '149,163,184';
            particles = []; beams = [];
            for (var i = 0; i < n; i++) {
                particles.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.09, 0.14), vy: rand(-0.34, -0.08),
                    r: rand(2.2, 7.2), a: rand(0.1, 0.32),
                    t: i % 5 === 0 ? C2 : (i % 3 === 0 ? C1 : C3)
                });
            }
            for (var j = 0; j < bn; j++) {
                beams.push({
                    off: rand(-scene.h * 0.7, scene.w + scene.h * 0.4),
                    spd: rand(0.1, 0.26), w: rand(18, 52),
                    a: rand(0.04, 0.11),
                    t: j % 2 === 0 ? C2 : C1
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);

            for (var b = 0; b < beams.length; b++) {
                var bm = beams[b];
                bm.off += bm.spd;
                if (bm.off > scene.w + scene.h * 0.8) bm.off = -scene.h * 0.75;

                var sx = bm.off, sy = -scene.h * 0.12;
                var ex = bm.off - scene.h * 0.72, ey = scene.h * 1.12;
                var g = ctx.createLinearGradient(sx, sy, ex, ey);
                g.addColorStop(0, 'rgba(' + bm.t + ',0)');
                g.addColorStop(0.25, 'rgba(' + bm.t + ',' + bm.a + ')');
                g.addColorStop(0.5, 'rgba(' + bm.t + ',' + (bm.a * 1.4) + ')');
                g.addColorStop(0.75, 'rgba(' + bm.t + ',' + bm.a + ')');
                g.addColorStop(1, 'rgba(' + bm.t + ',0)');
                ctx.beginPath(); ctx.strokeStyle = g; ctx.lineWidth = bm.w;
                ctx.moveTo(sx, sy); ctx.lineTo(ex, ey); ctx.stroke();

                ctx.beginPath();
                ctx.strokeStyle = 'rgba(255,255,255,' + (bm.a * 0.55) + ')';
                ctx.lineWidth = 1.2; ctx.setLineDash([14, 18]);
                ctx.moveTo(sx - bm.w * 0.45, sy); ctx.lineTo(ex - bm.w * 0.45, ey);
                ctx.stroke(); ctx.setLineDash([]);
            }

            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                p.x += p.vx; p.y += p.vy;
                applyRepel(p, scene);
                if (p.x <= -30 || p.x >= scene.w + 30) p.x = rand(0, scene.w);
                if (p.y <= -30) { p.y = scene.h + rand(10, 120); p.x = rand(0, scene.w); }

                var glow = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 4.4);
                glow.addColorStop(0, 'rgba(' + p.t + ',' + (p.a * 1.2) + ')');
                glow.addColorStop(0.55, 'rgba(' + p.t + ',' + p.a + ')');
                glow.addColorStop(1, 'rgba(' + p.t + ',0)');
                ctx.beginPath(); ctx.fillStyle = glow;
                ctx.arc(p.x, p.y, p.r * 4.4, 0, Math.PI * 2); ctx.fill();

                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + p.t + ',' + (p.a * 1.45) + ')';
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill();
            }
        };
    };

    // ─── 2. Bokeh: Soft Glowing Circles (Creative / Photography) ───
    effects.bokeh = function(canvas, ctx, scene) {
        var circles = [];

        function build() {
            var n = scene.w < 768 ? 12 : 20;
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '200,160,255';
            circles = [];
            for (var i = 0; i < n; i++) {
                circles.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.15, 0.15), vy: rand(-0.12, 0.12),
                    r: rand(25, 90), a: rand(0.04, 0.14),
                    t: i % 4 === 0 ? C2 : (i % 3 === 0 ? C1 : (i % 2 === 0 ? C3 : '255,255,255'))
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            for (var i = 0; i < circles.length; i++) {
                var c = circles[i];
                c.x += c.vx; c.y += c.vy;
                applyRepel(c, scene);
                if (c.x < -c.r * 2) c.x = scene.w + c.r;
                if (c.x > scene.w + c.r * 2) c.x = -c.r;
                if (c.y < -c.r * 2) c.y = scene.h + c.r;
                if (c.y > scene.h + c.r * 2) c.y = -c.r;

                var g = ctx.createRadialGradient(c.x, c.y, c.r * 0.2, c.x, c.y, c.r);
                g.addColorStop(0, 'rgba(' + c.t + ',' + (c.a * 1.6) + ')');
                g.addColorStop(0.5, 'rgba(' + c.t + ',' + c.a + ')');
                g.addColorStop(1, 'rgba(' + c.t + ',0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(c.x, c.y, c.r, 0, Math.PI * 2); ctx.fill();

                ctx.beginPath();
                ctx.strokeStyle = 'rgba(' + c.t + ',' + (c.a * 0.8) + ')';
                ctx.lineWidth = 1;
                ctx.arc(c.x, c.y, c.r * 0.7, 0, Math.PI * 2); ctx.stroke();
            }
        };
    };

    // ─── 3. Constellation: Stars with Connected Lines (Space / Luxury) ───
    effects.constellation = function(canvas, ctx, scene) {
        var dots = [], linkDist = 120;

        function build() {
            var n = scene.w < 768 ? 30 : 50;
            linkDist = scene.w < 768 ? 90 : 120;
            dots = [];
            for (var i = 0; i < n; i++) {
                dots.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.2, 0.2), vy: rand(-0.2, 0.2),
                    r: rand(1.5, 3), a: rand(0.35, 0.75)
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);

            for (var i = 0; i < dots.length; i++) {
                for (var j = i + 1; j < dots.length; j++) {
                    var dx = dots[i].x - dots[j].x, dy = dots[i].y - dots[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < linkDist) {
                        var alpha = (1 - dist / linkDist) * 0.18;
                        ctx.beginPath();
                        var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '180,200,230';
                        ctx.strokeStyle = 'rgba(' + C1 + ',' + alpha + ')';
                        ctx.lineWidth = 0.8;
                        ctx.moveTo(dots[i].x, dots[i].y);
                        ctx.lineTo(dots[j].x, dots[j].y);
                        ctx.stroke();
                    }
                }
            }

            for (var k = 0; k < dots.length; k++) {
                var d = dots[k];
                d.x += d.vx; d.y += d.vy;
                applyRepel(d, scene);
                if (d.x < 0 || d.x > scene.w) d.vx *= -1;
                if (d.y < 0 || d.y > scene.h) d.vy *= -1;

                ctx.beginPath();
                var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '220,230,245';
                ctx.fillStyle = 'rgba(' + C1 + ',' + d.a + ')';
                ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2); ctx.fill();

                var g = ctx.createRadialGradient(d.x, d.y, 0, d.x, d.y, d.r * 5);
                g.addColorStop(0, 'rgba(' + C1 + ',' + (d.a * 0.3) + ')');
                g.addColorStop(1, 'rgba(' + C1 + ',0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(d.x, d.y, d.r * 5, 0, Math.PI * 2); ctx.fill();
            }
        };
    };

    // ─── 4. Rain: Digital Falling Streaks (Technology / Digital) ───
    effects.rain = function(canvas, ctx, scene) {
        var drops = [];

        function build() {
            var n = scene.w < 768 ? 50 : 80;
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '180,200,220';
            drops = [];
            for (var i = 0; i < n; i++) {
                drops.push({
                    x: rand(0, scene.w), y: rand(-scene.h, scene.h),
                    len: rand(14, 40), spd: rand(2, 6),
                    a: rand(0.06, 0.22),
                    t: i % 3 === 0 ? C1 : C3
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            for (var i = 0; i < drops.length; i++) {
                var d = drops[i];
                d.y += d.spd;
                applyRepel(d, scene);
                if (d.y > scene.h + d.len) {
                    d.y = -d.len - rand(0, scene.h * 0.3);
                    d.x = rand(0, scene.w);
                }

                var g = ctx.createLinearGradient(d.x, d.y - d.len, d.x, d.y);
                g.addColorStop(0, 'rgba(' + d.t + ',0)');
                g.addColorStop(1, 'rgba(' + d.t + ',' + d.a + ')');
                ctx.beginPath(); ctx.strokeStyle = g;
                ctx.lineWidth = 1.2;
                ctx.moveTo(d.x, d.y - d.len);
                ctx.lineTo(d.x, d.y);
                ctx.stroke();

                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + d.t + ',' + (d.a * 1.5) + ')';
                ctx.arc(d.x, d.y, 1.2, 0, Math.PI * 2); ctx.fill();
            }
        };
    };

    // ─── 5. Waves: Flowing Sine Waves (Medical / Wellness / Ocean) ───
    effects.waves = function(canvas, ctx, scene) {
        var time = 0;
        var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
        var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
        var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '150,220,180';
        var colors = [C1, C2, C3, C1, C2];

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 0.008;

            for (var w = 0; w < 5; w++) {
                var amp = 22 + w * 14;
                var freq = 0.003 + w * 0.001;
                var yBase = scene.h * (0.22 + w * 0.13);
                var phase = time * (0.8 + w * 0.3) + w * 1.2;
                var alpha = 0.06 + w * 0.018;

                ctx.beginPath();
                ctx.moveTo(0, yBase);
                for (var x = 0; x <= scene.w; x += 4) {
                    var y = yBase + Math.sin(x * freq + phase) * amp + Math.sin(x * freq * 2.3 + phase * 1.4) * (amp * 0.3);
                    ctx.lineTo(x, y);
                }
                ctx.strokeStyle = 'rgba(' + colors[w] + ',' + alpha + ')';
                ctx.lineWidth = 2;
                ctx.stroke();

                ctx.lineTo(scene.w, scene.h);
                ctx.lineTo(0, scene.h);
                ctx.closePath();
                ctx.fillStyle = 'rgba(' + colors[w] + ',' + (alpha * 0.25) + ')';
                ctx.fill();
            }
        };
    };

    // ─── 6. Geometric: Floating Shapes (Architecture / Engineering) ───
    effects.geometric = function(canvas, ctx, scene) {
        var shapes = [];

        function build() {
            var n = scene.w < 768 ? 8 : 15;
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '180,200,220';
            shapes = [];
            for (var i = 0; i < n; i++) {
                shapes.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.2, 0.2), vy: rand(-0.2, 0.2),
                    r: rand(18, 52), rot: rand(0, Math.PI * 2),
                    rotSpd: rand(-0.005, 0.005),
                    sides: [3, 4, 5, 6][Math.floor(rand(0, 4))],
                    a: rand(0.07, 0.18),
                    t: i % 4 === 0 ? C2 : (i % 3 === 0 ? C1 : (i % 2 === 0 ? C3 : C1))
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            for (var i = 0; i < shapes.length; i++) {
                var s = shapes[i];
                s.x += s.vx; s.y += s.vy; s.rot += s.rotSpd;
                applyRepel(s, scene);
                if (s.x < -s.r * 2) s.x = scene.w + s.r;
                if (s.x > scene.w + s.r * 2) s.x = -s.r;
                if (s.y < -s.r * 2) s.y = scene.h + s.r;
                if (s.y > scene.h + s.r * 2) s.y = -s.r;

                ctx.save();
                ctx.translate(s.x, s.y);
                ctx.rotate(s.rot);
                ctx.beginPath();
                for (var j = 0; j <= s.sides; j++) {
                    var angle = (Math.PI * 2 / s.sides) * j - Math.PI / 2;
                    var px = Math.cos(angle) * s.r;
                    var py = Math.sin(angle) * s.r;
                    if (j === 0) ctx.moveTo(px, py); else ctx.lineTo(px, py);
                }
                ctx.closePath();
                ctx.strokeStyle = 'rgba(' + s.t + ',' + s.a + ')';
                ctx.lineWidth = 1.5;
                ctx.stroke();
                ctx.fillStyle = 'rgba(' + s.t + ',' + (s.a * 0.15) + ')';
                ctx.fill();
                ctx.restore();
            }
        };
    };

    // ─── 7. Snow: Gentle Snowfall (Clean / Minimal) ───
    effects.snow = function(canvas, ctx, scene) {
        var flakes = [];
        var time = 0;

        function build() {
            var n = scene.w < 768 ? 35 : 55;
            flakes = [];
            for (var i = 0; i < n; i++) {
                flakes.push({
                    x: rand(0, scene.w), y: rand(-scene.h * 0.2, scene.h),
                    r: rand(1.5, 4.5), spd: rand(0.4, 1.5),
                    drift: rand(-0.3, 0.3), a: rand(0.15, 0.45),
                    phase: rand(0, Math.PI * 2)
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 0.01;
            for (var i = 0; i < flakes.length; i++) {
                var f = flakes[i];
                f.y += f.spd;
                f.x += f.drift + Math.sin(time + f.phase) * 0.3;
                applyRepel(f, scene);
                if (f.y > scene.h + f.r * 2) { f.y = -f.r * 2; f.x = rand(0, scene.w); }
                if (f.x < -10) f.x = scene.w + 5;
                if (f.x > scene.w + 10) f.x = -5;

                var g = ctx.createRadialGradient(f.x, f.y, 0, f.x, f.y, f.r * 3);
                g.addColorStop(0, 'rgba(255,255,255,' + f.a + ')');
                g.addColorStop(0.5, 'rgba(230,240,255,' + (f.a * 0.4) + ')');
                g.addColorStop(1, 'rgba(255,255,255,0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(f.x, f.y, f.r * 3, 0, Math.PI * 2); ctx.fill();

                ctx.beginPath();
                ctx.fillStyle = 'rgba(255,255,255,' + (f.a * 1.2) + ')';
                ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2); ctx.fill();
            }
        };
    };

    // ─── 8. Fireflies: Pulsing Glow Dots (Nature / Organic) ───
    effects.fireflies = function(canvas, ctx, scene) {
        var flies = [];
        var time = 0;

        function build() {
            var n = scene.w < 768 ? 16 : 28;
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '255,220,100';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '120,255,160';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '180,255,200';
            flies = [];
            for (var i = 0; i < n; i++) {
                flies.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.2, 0.2), vy: rand(-0.2, 0.2),
                    r: rand(2, 5), baseA: rand(0.08, 0.28),
                    phase: rand(0, Math.PI * 2), pulse: rand(0.8, 2.5),
                    t: i % 3 === 0 ? C2 : (i % 2 === 0 ? C3 : C1)
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 0.016;
            for (var i = 0; i < flies.length; i++) {
                var f = flies[i];
                f.x += f.vx + Math.sin(time * 0.5 + f.phase) * 0.15;
                f.y += f.vy + Math.cos(time * 0.4 + f.phase) * 0.15;
                applyRepel(f, scene);
                if (f.x < -20) f.x = scene.w + 10;
                if (f.x > scene.w + 20) f.x = -10;
                if (f.y < -20) f.y = scene.h + 10;
                if (f.y > scene.h + 20) f.y = -10;

                var pulse = (Math.sin(time * f.pulse + f.phase) + 1) * 0.5;
                var a = f.baseA * (0.2 + pulse * 0.8);

                var g = ctx.createRadialGradient(f.x, f.y, 0, f.x, f.y, f.r * 6);
                g.addColorStop(0, 'rgba(' + f.t + ',' + (a * 1.5) + ')');
                g.addColorStop(0.3, 'rgba(' + f.t + ',' + a + ')');
                g.addColorStop(1, 'rgba(' + f.t + ',0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(f.x, f.y, f.r * 6, 0, Math.PI * 2); ctx.fill();

                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + f.t + ',' + Math.min(1, a * 2.2) + ')';
                ctx.arc(f.x, f.y, f.r * (0.5 + pulse * 0.5), 0, Math.PI * 2);
                ctx.fill();
            }
        };
    };

    // ─── 9. Network: Neural Mesh (Technology / Data / AI) ───
    effects.network = function(canvas, ctx, scene) {
        var nodes = [], bgParticles = [], rays = [];
        var linkDist = 160, triDist = 130;
        var time = 0;

        function build() {
            var scale = (window.__hspPerf && window.__hspPerf.scale) || 1;
            var n  = Math.max(10, Math.round((scene.w < 768 ? 16 : 26) * scale));
            var bp = Math.max(4, Math.round((scene.w < 768 ? 6 : 10) * scale));
            var rn = scene.w < 768 ? 1  : (window.__hspPerf && window.__hspPerf.lowPower ? 1 : 2);
            linkDist = scene.w < 768 ? 100 : 140;
            triDist  = scene.w < 768 ? 80  : 110;
            nodes = []; bgParticles = []; rays = [];

            for (var i = 0; i < n; i++) {
                nodes.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    ox: 0, oy: 0,
                    vx: rand(-0.25, 0.25), vy: rand(-0.25, 0.25),
                    r: rand(1.8, 4.2), a: rand(0.5, 1),
                    pulse: rand(0, Math.PI * 2)
                });
            }

            for (var j = 0; j < bp; j++) {
                bgParticles.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.08, 0.08), vy: rand(-0.08, 0.08),
                    r: rand(20, 60), a: rand(0.03, 0.09)
                });
            }

            for (var k = 0; k < rn; k++) {
                rays.push({
                    x: rand(scene.w * 0.1, scene.w * 0.9),
                    y: rand(-scene.h * 0.2, scene.h * 0.3),
                    angle: rand(-0.9, -0.3),
                    len: rand(scene.h * 0.8, scene.h * 1.5),
                    w: rand(1.5, 4),
                    a: rand(0.04, 0.12),
                    spd: rand(0.0004, 0.0012)
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 0.012;

            // Background glow particles
            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '0,200,255';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '0,160,240';

            for (var b = 0; b < bgParticles.length; b++) {
                var bp = bgParticles[b];
                bp.x += bp.vx; bp.y += bp.vy;
                if (bp.x < -bp.r * 2) bp.x = scene.w + bp.r;
                if (bp.x > scene.w + bp.r * 2) bp.x = -bp.r;
                if (bp.y < -bp.r * 2) bp.y = scene.h + bp.r;
                if (bp.y > scene.h + bp.r * 2) bp.y = -bp.r;

                var bg = ctx.createRadialGradient(bp.x, bp.y, 0, bp.x, bp.y, bp.r);
                bg.addColorStop(0, 'rgba(' + C3 + ',' + (bp.a * 1.4) + ')');
                bg.addColorStop(0.5, 'rgba(' + C3 + ',' + bp.a + ')');
                bg.addColorStop(1, 'rgba(' + C3 + ',0)');
                ctx.beginPath(); ctx.fillStyle = bg;
                ctx.arc(bp.x, bp.y, bp.r, 0, Math.PI * 2); ctx.fill();
            }

            // Light rays
            for (var r = 0; r < rays.length; r++) {
                var ray = rays[r];
                ray.angle += ray.spd;
                var ex = ray.x + Math.cos(ray.angle) * ray.len;
                var ey = ray.y + Math.sin(ray.angle) * ray.len;
                var rg = ctx.createLinearGradient(ray.x, ray.y, ex, ey);
                rg.addColorStop(0, 'rgba(' + C1 + ',' + (ray.a * 1.2) + ')');
                rg.addColorStop(0.4, 'rgba(' + C1 + ',' + ray.a + ')');
                rg.addColorStop(1, 'rgba(' + C1 + ',0)');
                ctx.beginPath(); ctx.strokeStyle = rg;
                ctx.lineWidth = ray.w; ctx.globalAlpha = 0.7;
                ctx.moveTo(ray.x, ray.y); ctx.lineTo(ex, ey);
                ctx.stroke(); ctx.globalAlpha = 1;
            }

            // Move nodes
            for (var i = 0; i < nodes.length; i++) {
                var nd = nodes[i];
                nd.x += nd.vx; nd.y += nd.vy;
                applyRepel(nd, scene);
                if (nd.x < -40) nd.x = scene.w + 20;
                if (nd.x > scene.w + 40) nd.x = -20;
                if (nd.y < -40) nd.y = scene.h + 20;
                if (nd.y > scene.h + 40) nd.y = -20;
            }

            // Triangular mesh fill (skip on low-power — O(n^3) is the main CPU hog)
            if (!(window.__hspPerf && window.__hspPerf.lowPower)) {
                for (var i = 0; i < nodes.length; i++) {
                    for (var j = i + 1; j < nodes.length; j++) {
                        var dx1 = nodes[i].x - nodes[j].x, dy1 = nodes[i].y - nodes[j].y;
                        var d1 = Math.sqrt(dx1 * dx1 + dy1 * dy1);
                        if (d1 > triDist) continue;
                        for (var k = j + 1; k < nodes.length; k++) {
                            var dx2 = nodes[i].x - nodes[k].x, dy2 = nodes[i].y - nodes[k].y;
                            var dx3 = nodes[j].x - nodes[k].x, dy3 = nodes[j].y - nodes[k].y;
                            var d2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);
                            var d3 = Math.sqrt(dx3 * dx3 + dy3 * dy3);
                            if (d2 < triDist && d3 < triDist) {
                                var avg = (d1 + d2 + d3) / 3;
                                var ta = (1 - avg / triDist) * 0.035;
                                ctx.beginPath();
                                ctx.moveTo(nodes[i].x, nodes[i].y);
                                ctx.lineTo(nodes[j].x, nodes[j].y);
                                ctx.lineTo(nodes[k].x, nodes[k].y);
                                ctx.closePath();
                                ctx.fillStyle = 'rgba(' + C2 + ',' + ta + ')';
                                ctx.fill();
                            }
                        }
                    }
                }
            }

            // Connection lines
            for (var i = 0; i < nodes.length; i++) {
                for (var j = i + 1; j < nodes.length; j++) {
                    var dx = nodes[i].x - nodes[j].x, dy = nodes[i].y - nodes[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < linkDist) {
                        var la = (1 - dist / linkDist) * 0.35;
                        ctx.beginPath();
                        ctx.strokeStyle = 'rgba(' + C1 + ',' + la + ')';
                        ctx.lineWidth = (1 - dist / linkDist) * 1.8 + 0.3;
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.stroke();
                    }
                }
            }

            // Nodes (bright cyan dots with glow)
            for (var i = 0; i < nodes.length; i++) {
                var nd = nodes[i];
                var pulse = (Math.sin(time * 2.2 + nd.pulse) + 1) * 0.5;
                var nr = nd.r * (0.8 + pulse * 0.4);
                var na = nd.a * (0.6 + pulse * 0.4);

                // Outer glow
                var g1 = ctx.createRadialGradient(nd.x, nd.y, 0, nd.x, nd.y, nr * 8);
                g1.addColorStop(0, 'rgba(' + C3 + ',' + (na * 0.5) + ')');
                g1.addColorStop(0.3, 'rgba(' + C3 + ',' + (na * 0.2) + ')');
                g1.addColorStop(1, 'rgba(' + C3 + ',0)');
                ctx.beginPath(); ctx.fillStyle = g1;
                ctx.arc(nd.x, nd.y, nr * 8, 0, Math.PI * 2); ctx.fill();

                // Inner bright core
                var g2 = ctx.createRadialGradient(nd.x, nd.y, 0, nd.x, nd.y, nr * 2);
                g2.addColorStop(0, 'rgba(' + C1 + ',' + Math.min(1, na * 1.8) + ')');
                g2.addColorStop(0.4, 'rgba(' + C1 + ',' + na + ')');
                g2.addColorStop(1, 'rgba(' + C1 + ',0)');
                ctx.beginPath(); ctx.fillStyle = g2;
                ctx.arc(nd.x, nd.y, nr * 2, 0, Math.PI * 2); ctx.fill();

                // Solid core
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + C1 + ',' + Math.min(1, na * 2) + ')';
                ctx.arc(nd.x, nd.y, nr * 0.6, 0, Math.PI * 2); ctx.fill();
            }
        };
    };

    // ─── 9. Tourism Breeze: Sun & Palm Icons (Travel / Tourism) ───
    effects.tourism = function(canvas, ctx, scene) {
        var particles = [];
        function build() {
            var n = scene.w < 768 ? 12 : 20;
            var sunColor = '255,215,0'; // bright sun
            var palmColor = '34,139,34'; // palm green
            particles = [];
            for (var i = 0; i < n; i++) {
                particles.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.05, 0.05), vy: rand(-0.05, 0.05),
                    r: rand(15, 30), a: rand(0.2, 0.5),
                    t: (i % 2 === 0) ? sunColor : palmColor
                });
            }
        }
        build();
        scene.onBuild = build;
        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                p.x += p.vx; p.y += p.vy;
                applyRepel(p, scene);
                if (p.x < -p.r * 2) p.x = scene.w + p.r;
                if (p.x > scene.w + p.r * 2) p.x = -p.r;
                if (p.y < -p.r * 2) p.y = scene.h + p.r;
                if (p.y > scene.h + p.r * 2) p.y = -p.r;
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + p.t + ',' + p.a + ')';
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();
            }
        };
    };
    // ─── 10. Academy: Soft Knowledge Network (Education / Academic) ───
    effects.academy = function(canvas, ctx, scene) {
        var nodes = [], orbs = [], time = 0;
        var linkDist = 140;

        function build() {
            var n = scene.w < 768 ? 20 : 36;
            var o = scene.w < 768 ? 5 : 10;
            linkDist = scene.w < 768 ? 100 : 140;
            nodes = []; orbs = [];

            var C1 = scene.palette ? scene.palette.rgb(scene.palette.c1) : '100,180,255';
            var C2 = scene.palette ? scene.palette.rgb(scene.palette.c2) : '255,176,92';
            var C3 = scene.palette ? scene.palette.rgb(scene.palette.c3) : '150,220,180';

            for (var i = 0; i < n; i++) {
                nodes.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.12, 0.12), vy: rand(-0.12, 0.12),
                    r: rand(2, 4.5), a: rand(0.3, 0.7),
                    t: i % 3 === 0 ? C2 : (i % 2 === 0 ? C1 : C3),
                    phase: rand(0, Math.PI * 2)
                });
            }
            for (var j = 0; j < o; j++) {
                orbs.push({
                    x: rand(0, scene.w), y: rand(0, scene.h),
                    vx: rand(-0.08, 0.08), vy: rand(-0.08, 0.08),
                    r: rand(30, 80), a: rand(0.03, 0.08),
                    t: j % 3 === 0 ? C1 : (j % 2 === 0 ? C2 : C3)
                });
            }
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 0.008;

            // Soft background orbs
            for (var j = 0; j < orbs.length; j++) {
                var o = orbs[j];
                o.x += o.vx; o.y += o.vy;
                applyRepel(o, scene);
                if (o.x < -o.r * 2) o.x = scene.w + o.r;
                if (o.x > scene.w + o.r * 2) o.x = -o.r;
                if (o.y < -o.r * 2) o.y = scene.h + o.r;
                if (o.y > scene.h + o.r * 2) o.y = -o.r;

                var g = ctx.createRadialGradient(o.x, o.y, 0, o.x, o.y, o.r);
                g.addColorStop(0, 'rgba(' + o.t + ',' + (o.a * 1.5) + ')');
                g.addColorStop(0.6, 'rgba(' + o.t + ',' + o.a + ')');
                g.addColorStop(1, 'rgba(' + o.t + ',0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(o.x, o.y, o.r, 0, Math.PI * 2); ctx.fill();
            }

            // Connection lines between nearby nodes (knowledge web)
            for (var i = 0; i < nodes.length; i++) {
                for (var j = i + 1; j < nodes.length; j++) {
                    var dx = nodes[i].x - nodes[j].x, dy = nodes[i].y - nodes[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < linkDist) {
                        var la = (1 - dist / linkDist) * 0.2;
                        ctx.beginPath();
                        ctx.strokeStyle = 'rgba(' + nodes[i].t + ',' + la + ')';
                        ctx.lineWidth = 0.8;
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.stroke();
                    }
                }
            }

            // Draw knowledge nodes
            for (var i = 0; i < nodes.length; i++) {
                var nd = nodes[i];
                nd.x += nd.vx; nd.y += nd.vy;
                applyRepel(nd, scene);
                if (nd.x < 0 || nd.x > scene.w) nd.vx *= -1;
                if (nd.y < 0 || nd.y > scene.h) nd.vy *= -1;

                var pulse = (Math.sin(time * 1.5 + nd.phase) + 1) * 0.5;
                var nr = nd.r * (0.8 + pulse * 0.3);

                // Glow
                var g = ctx.createRadialGradient(nd.x, nd.y, 0, nd.x, nd.y, nr * 6);
                g.addColorStop(0, 'rgba(' + nd.t + ',' + (nd.a * 0.4) + ')');
                g.addColorStop(1, 'rgba(' + nd.t + ',0)');
                ctx.beginPath(); ctx.fillStyle = g;
                ctx.arc(nd.x, nd.y, nr * 6, 0, Math.PI * 2); ctx.fill();

                // Core
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + nd.t + ',' + nd.a + ')';
                ctx.arc(nd.x, nd.y, nr, 0, Math.PI * 2); ctx.fill();
            }

            // Subtle floating shapes (pages / books)
            for (var k = 0; k < 3; k++) {
                var sx = (scene.w * 0.15) + Math.sin(time * 0.4 + k * 2.1) * (scene.w * 0.35);
                var sy = (scene.h * 0.2) + Math.cos(time * 0.3 + k * 1.7) * (scene.h * 0.3);
                var rot = time * 0.2 + k;
                var size = 12 + k * 6;

                ctx.save();
                ctx.translate(sx, sy);
                ctx.rotate(rot);
                ctx.beginPath();
                ctx.rect(-size / 2, -size / 2, size, size);
                ctx.strokeStyle = 'rgba(' + scene.palette.rgb(scene.palette.c1) + ',' + (0.06 + k * 0.02) + ')';
                ctx.lineWidth = 1;
                ctx.stroke();
                ctx.fillStyle = 'rgba(' + scene.palette.rgb(scene.palette.c2) + ',' + (0.03 + k * 0.01) + ')';
                ctx.fill();
                ctx.restore();
            }
        };
    };

    // ─── 11. Autumn Leaves ───
    effects.autumn = function(canvas, ctx, scene) {
        var leaves = [], time = 0;

        // Autumn palette (warm tones)
        var palette = [
            '229,115,45',  // orange
            '255,193,7',   // amber
            '139,69,19',   // saddle brown
            '194,74,32',   // burnt sienna
            '212,175,55',  // gold
            '205,92,92',   // indian red
            '218,165,32',  // golden rod
            '160,82,45',   // sienna
        ];

        function build() {
            var count = scene.w < 768 ? 18 : 32;
            leaves = [];
            for (var i = 0; i < count; i++) {
                leaves.push({
                    x: rand(0, scene.w),
                    y: rand(-scene.h, scene.h),
                    vx: rand(-0.3, 0.3),
                    vy: rand(0.4, 1.2),
                    r: rand(6, 14),
                    rot: rand(0, Math.PI * 2),
                    rotSpeed: rand(-0.02, 0.02),
                    swayAmp: rand(15, 45),
                    swayFreq: rand(0.003, 0.008),
                    swayPhase: rand(0, Math.PI * 2),
                    color: palette[Math.floor(rand(0, palette.length))],
                    opacity: rand(0.55, 0.95),
                    type: Math.floor(rand(0, 3)) // 0: oval leaf, 1: pointed leaf, 2: maple-ish
                });
            }
        }

        function drawLeaf(l) {
            ctx.save();
            ctx.translate(l.x, l.y);
            ctx.rotate(l.rot);
            ctx.globalAlpha = l.opacity;
            ctx.fillStyle = 'rgb(' + l.color + ')';
            ctx.strokeStyle = 'rgba(' + l.color + ',0.6)';
            ctx.lineWidth = 0.6;

            if (l.type === 0) {
                // Oval leaf
                ctx.beginPath();
                ctx.ellipse(0, 0, l.r, l.r * 0.55, 0, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();
                // vein line
                ctx.beginPath();
                ctx.moveTo(0, -l.r * 0.4);
                ctx.lineTo(0, l.r * 0.4);
                ctx.strokeStyle = 'rgba(0,0,0,0.15)';
                ctx.stroke();
            } else if (l.type === 1) {
                // Pointed leaf (ellipse with tapered top)
                ctx.beginPath();
                ctx.moveTo(0, -l.r);
                ctx.bezierCurveTo(l.r * 0.6, -l.r * 0.3, l.r * 0.5, l.r * 0.5, 0, l.r * 0.7);
                ctx.bezierCurveTo(-l.r * 0.5, l.r * 0.5, -l.r * 0.6, -l.r * 0.3, 0, -l.r);
                ctx.fill();
                ctx.stroke();
                // vein
                ctx.beginPath();
                ctx.moveTo(0, -l.r * 0.7);
                ctx.lineTo(0, l.r * 0.5);
                ctx.strokeStyle = 'rgba(0,0,0,0.15)';
                ctx.stroke();
            } else {
                // Rounded leaf with slight indent
                ctx.beginPath();
                ctx.moveTo(0, -l.r);
                ctx.quadraticCurveTo(l.r * 0.7, -l.r * 0.2, l.r * 0.5, l.r * 0.5);
                ctx.quadraticCurveTo(0, l.r * 0.85, 0, l.r * 0.5);
                ctx.quadraticCurveTo(0, l.r * 0.85, -l.r * 0.5, l.r * 0.5);
                ctx.quadraticCurveTo(-l.r * 0.7, -l.r * 0.2, 0, -l.r);
                ctx.fill();
                ctx.stroke();
            }

            ctx.restore();
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 1;

            for (var i = 0; i < leaves.length; i++) {
                var l = leaves[i];

                // Sway movement
                var sway = Math.sin(time * l.swayFreq + l.swayPhase) * l.swayAmp;
                l.x += l.vx + sway * 0.015;
                l.y += l.vy;
                l.rot += l.rotSpeed;

                // Reset when off bottom
                if (l.y > scene.h + l.r * 2) {
                    l.y = -l.r * 2;
                    l.x = rand(0, scene.w);
                    l.opacity = rand(0.55, 0.95);
                }
                // Wrap horizontal
                if (l.x < -l.r * 2) l.x = scene.w + l.r;
                if (l.x > scene.w + l.r * 2) l.x = -l.r;

                drawLeaf(l);
            }
        };
    };

    // ─── 12. Exhaust Smoke ───
    effects.exhaust = function(canvas, ctx, scene) {
        var puffs = [], time = 0;

        // Stronger contrast palette — includes lighter tones for visibility
        var smokeColors = [
            '180,180,180', // light gray (visible against dark bg)
            '140,140,140', // medium light
            '90,90,90',    // mid gray
            '55,55,55',    // dark gray
            '30,30,30',    // charcoal
            '200,200,200', // near white steam edge
        ];

        // Fixed exhaust port positions (bottom of screen)
        var ports = [];
        function initPorts() {
            ports = [
                { x: scene.w * 0.20 },
                { x: scene.w * 0.50 },
                { x: scene.w * 0.80 },
            ];
        }

        function build() {
            initPorts();
            var count = scene.w < 768 ? 50 : 85;
            puffs = [];
            for (var i = 0; i < count; i++) {
                var port = ports[Math.floor(rand(0, ports.length))];
                var startX = port.x + rand(-30, 30);
                puffs.push({
                    x: startX,
                    y: scene.h + rand(10, 90),
                    vx: rand(-0.3, 0.3),
                    vy: rand(-0.8, -2.2),
                    baseR: rand(12, 32),
                    r: rand(12, 32),
                    opacity: rand(0.35, 0.8),
                    maxOpacity: rand(0.35, 0.8),
                    color: smokeColors[Math.floor(rand(0, smokeColors.length))],
                    life: rand(0, 60),
                    maxLife: rand(120, 260),
                    swayAmp: rand(20, 60),
                    swayFreq: rand(0.003, 0.009),
                    swayPhase: rand(0, Math.PI * 2),
                    growRate: rand(0.12, 0.35),
                    glow: rand(0, 1) > 0.6,
                });
            }
        }

        function resetPuff(p) {
            var port = ports[Math.floor(rand(0, ports.length))];
            p.x = port.x + rand(-35, 35);
            p.y = scene.h + rand(10, 60);
            p.vx = rand(-0.3, 0.3);
            p.vy = rand(-0.8, -2.2);
            p.r = p.baseR;
            p.opacity = p.maxOpacity;
            p.life = 0;
            p.swayPhase = rand(0, Math.PI * 2);
            p.growRate = rand(0.12, 0.35);
            p.glow = rand(0, 1) > 0.6;
        }

        function drawPuff(p) {
            // Soft radial gradient
            var g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
            var alpha = Math.max(0, p.opacity).toFixed(3);
            g.addColorStop(0, 'rgba(' + p.color + ',' + alpha + ')');
            g.addColorStop(0.35, 'rgba(' + p.color + ',' + (alpha * 0.75).toFixed(3) + ')');
            g.addColorStop(0.7, 'rgba(' + p.color + ',' + (alpha * 0.35).toFixed(3) + ')');
            g.addColorStop(1, 'rgba(' + p.color + ',0)');

            if (p.glow) {
                ctx.shadowBlur = p.r * 0.6;
                ctx.shadowColor = 'rgba(' + p.color + ',' + (alpha * 0.4).toFixed(3) + ')';
            } else {
                ctx.shadowBlur = 0;
                ctx.shadowColor = 'transparent';
            }

            ctx.beginPath();
            ctx.fillStyle = g;
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fill();

            ctx.shadowBlur = 0;
        }

        build();
        scene.onBuild = build;

        return function() {
            ctx.clearRect(0, 0, scene.w, scene.h);
            time += 1;

            // Re-init ports on resize
            if (ports.length === 0 || Math.abs(ports[1].x - scene.w * 0.50) > 10) {
                initPorts();
            }

            for (var i = 0; i < puffs.length; i++) {
                var p = puffs[i];

                p.life += 1;

                // Sway movement (wind drift)
                var sway = Math.sin(time * p.swayFreq + p.swayPhase) * p.swayAmp;
                p.x += p.vx + sway * 0.012;
                p.y += p.vy;

                // Expand as it rises
                p.r += p.growRate;

                // Fade out over life
                var lifeRatio = p.life / p.maxLife;
                if (lifeRatio > 0.4) {
                    p.opacity = p.maxOpacity * Math.max(0, (1 - (lifeRatio - 0.4) / 0.6));
                }

                // Mouse repel adds turbulence
                if (scene.mx >= 0 && scene.my >= 0) {
                    var dx = p.x - scene.mx;
                    var dy = p.y - scene.my;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 140 && dist > 0.1) {
                        var force = (140 - dist) / 140;
                        p.x += (dx / dist) * force * 1.0;
                        p.y += (dy / dist) * force * 0.5;
                    }
                }

                // Reset when too high or fully faded
                if (p.y < -p.r * 2 || p.opacity <= 0.01 || p.life > p.maxLife) {
                    resetPuff(p);
                }

                // Wrap horizontal
                if (p.x < -p.r * 2) p.x = scene.w + p.r;
                if (p.x > scene.w + p.r * 2) p.x = -p.r;

                drawPuff(p);
            }
        };
    };

    // ─── Initialize Effects ───
    $(function() {
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduceMotion) return;

        var cores = navigator.hardwareConcurrency || 4;
        var mem = navigator.deviceMemory || 8;
        var narrow = window.matchMedia && window.matchMedia('(max-width: 900px)').matches;
        var saveData = !!(navigator.connection && navigator.connection.saveData);
        var lowPower = cores <= 4 || mem <= 4 || narrow || saveData;
        var targetFps = lowPower ? 16 : 22;
        var frameInterval = 1000 / targetFps;
        var maxDpr = lowPower ? 1 : Math.min(window.devicePixelRatio || 1, 1.25);

        window.__hspPerf = {
            lowPower: lowPower,
            scale: lowPower ? 0.55 : 0.85,
            fps: targetFps
        };

        try {
            document.documentElement.classList.add(lowPower ? 'mrslia-low-power' : 'mrslia-perf-ok');
        } catch (e) {}

        $('.hero-slider-section').each(function() {
            var $section = $(this);
            var section = this;
            var effectType = $section.data('effect') || 'plexus';
            if (effectType === 'none' || !effects[effectType]) return;

            var controllers = [];

            function syncActiveLoops() {
                controllers.forEach(function(ctrl) {
                    if (ctrl.shouldRun()) {
                        ctrl.start();
                    } else {
                        ctrl.stop();
                    }
                });
            }

            $section.find('.hero-slide-item:not(.slick-cloned) .hero-plexus').each(function() {
                var canvas = this;
                if (canvas.dataset.initialized === 'true' || !canvas.getContext) return;
                canvas.dataset.initialized = 'true';

                var ctx = canvas.getContext('2d', { alpha: true, desynchronized: true });
                var host = canvas.parentElement;
                var slide = canvas.closest('.slick-slide') || host.closest('.slick-slide');
                var cs = window.getComputedStyle(section);
                var eff1 = cs.getPropertyValue('--hsp-eff1').trim() || '#64b4ff';
                var eff2 = cs.getPropertyValue('--hsp-eff2').trim() || '#ffb05c';
                var eff3 = cs.getPropertyValue('--hsp-eff3').trim() || '#00c8ff';
                var dpr = maxDpr;
                var scene = { w: 0, h: 0, mx: -1, my: -1, onBuild: null };
                scene.palette = {
                    c1: eff1,
                    c2: eff2,
                    c3: eff3,
                    rgb: function(hex){
                        var h = hex.replace('#','');
                        if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
                        var r = parseInt(h.substr(0,2),16), g = parseInt(h.substr(2,2),16), b = parseInt(h.substr(4,2),16);
                        return r+','+g+','+b;
                    }
                };

                // Mouse repel only on desktop with a fine pointer (saves mobile GPU/CPU).
                var finePointer = window.matchMedia && window.matchMedia('(pointer: fine)').matches;
                if (finePointer && !lowPower) {
                    canvas.addEventListener('mousemove', function(e) {
                        var rect = canvas.getBoundingClientRect();
                        scene.mx = e.clientX - rect.left;
                        scene.my = e.clientY - rect.top;
                    }, { passive: true });
                    canvas.addEventListener('mouseleave', function() {
                        scene.mx = -1;
                        scene.my = -1;
                    });
                }

                function resize() {
                    var r = host.getBoundingClientRect();
                    scene.w = Math.max(1, r.width);
                    scene.h = Math.max(1, r.height);
                    canvas.width = Math.floor(scene.w * dpr);
                    canvas.height = Math.floor(scene.h * dpr);
                    canvas.style.width = scene.w + 'px';
                    canvas.style.height = scene.h + 'px';
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                    if (scene.onBuild) scene.onBuild();
                }

                resize();
                window.addEventListener('resize', resize);

                var render = effects[effectType](canvas, ctx, scene);
                var rafId = 0;
                var sectionVisible = true;
                var lastDraw = 0;
                var isFirefox = typeof InstallTrigger !== 'undefined'
                    || (typeof navigator !== 'undefined' && /firefox/i.test(navigator.userAgent || ''));

                function drawFrame() {
                    try {
                        render();
                    } catch (err) {}
                }

                function isSlideActive() {
                    if (!slide) return true;
                    if (slide.classList.contains('slick-cloned')) return false;
                    // Prefer current; fall back to active for fade/center modes.
                    if (slide.classList.contains('slick-current')) return true;
                    var currents = section.querySelectorAll('.slick-slide.slick-current:not(.slick-cloned)');
                    if (currents.length) return false;
                    return slide.classList.contains('slick-active');
                }

                function shouldRun() {
                    return sectionVisible && !document.hidden && isSlideActive();
                }

                function loop(ts) {
                    if (!shouldRun()) {
                        rafId = 0;
                        return;
                    }
                    if (!lastDraw || (ts - lastDraw) >= frameInterval) {
                        lastDraw = ts;
                        drawFrame();
                    }
                    rafId = window.requestAnimationFrame(loop);
                }

                function startLoop() {
                    if (rafId || !shouldRun()) return;
                    rafId = window.requestAnimationFrame(loop);
                }

                function stopLoop() {
                    if (rafId) {
                        window.cancelAnimationFrame(rafId);
                        rafId = 0;
                    }
                }

                canvas.__hspForceRedraw = function() {
                    resize();
                    drawFrame();
                    startLoop();
                };

                canvas.__hspStop = stopLoop;
                canvas.__hspStart = startLoop;

                controllers.push({
                    canvas: canvas,
                    shouldRun: shouldRun,
                    start: startLoop,
                    stop: stopLoop,
                    setSectionVisible: function(v) {
                        sectionVisible = !!v;
                        if (!sectionVisible) stopLoop();
                    }
                });

                // Observe the canvas host (not the whole slider) so off-screen slides stay idle.
                if (typeof IntersectionObserver === 'function') {
                    var io = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            sectionVisible = entry.isIntersecting;
                            if (entry.isIntersecting) {
                                canvas.__hspForceRedraw();
                                syncActiveLoops();
                            } else {
                                stopLoop();
                            }
                        });
                    }, { root: null, threshold: 0.05, rootMargin: '40px 0px' });
                    io.observe(host);
                }

                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        stopLoop();
                    } else if (isFirefox || shouldRun()) {
                        canvas.__hspForceRedraw();
                        syncActiveLoops();
                    }
                });

                if (isFirefox) {
                    window.addEventListener('pageshow', function() {
                        canvas.__hspForceRedraw();
                        syncActiveLoops();
                    });
                }
            });

            // Only the active slick slide keeps its canvas loop running.
            $section.on('init reInit afterChange', function() {
                syncActiveLoops();
            });
            // Slick may already be initialized before this script runs.
            setTimeout(syncActiveLoops, 0);
            setTimeout(syncActiveLoops, 400);
        });
    });

})(jQuery);
