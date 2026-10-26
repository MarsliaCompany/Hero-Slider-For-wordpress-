<?php
/**
 * Hero Slider Plugin — Frontend Styles
 *
 * @copyright 2025 Mahmoud Mahsob / Marslia (https://mrslia.com)
 * @author    Mahmoud Mahsob <https://mrslia.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hero_slider_plugin_mark_shortcode_used() {
    $GLOBALS['hero_slider_plugin_shortcode_used'] = true;
}

function hero_slider_plugin_is_shortcode_used() {
    return ! empty( $GLOBALS['hero_slider_plugin_shortcode_used'] );
}

function hero_slider_plugin_styles() {
    if ( ! hero_slider_plugin_is_shortcode_used() ) {
        return;
    }

    static $hero_slider_styles_added = false;
    if ( $hero_slider_styles_added ) {
        return;
    }
    $hero_slider_styles_added = true;

    wp_register_style( 'hero-slider-plugin', false, array(), HERO_SLIDER_PLUGIN_VERSION );
    wp_enqueue_style( 'hero-slider-plugin' );

    $css = '
    .hero-slider-section {
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 100%;
        margin: 0;
        z-index: 0;
        isolation: isolate;
        /* Avoid CSS contain — Firefox drops compositor layers on scroll (black flash). */
        contain: none;
        transform: translateZ(0);
        backface-visibility: hidden;
        box-sizing: border-box;
        height: var(--hero-height);
        min-height: var(--hero-height);
        max-height: var(--hero-height);
        /* مقاييس متناسقة مع ارتفاع المجموعة — تباعد متوازن بين النص والصورة والأزرار */
        --hsp-title-size: clamp(22px, calc(var(--hero-height) * 0.056), 48px);
        --hsp-text-size: clamp(14px, calc(var(--hero-height) * 0.022), 18px);
        --hsp-img-max-h: calc(var(--hero-height) * 0.78);
        --hsp-content-pad-y: clamp(16px, calc(var(--hero-height) * 0.05), 52px);
        --hsp-visual-pad-y: clamp(12px, calc(var(--hero-height) * 0.04), 48px);
        --hsp-actions-mt: clamp(10px, calc(var(--hero-height) * 0.022), 28px);
        --hsp-col-gap: clamp(12px, 1.6vw, 28px);
        --hsp-stack-gap: clamp(12px, 1.8vw, 20px);
        background:
            radial-gradient(circle at 18% 18%, rgba(196, 150, 98, 0.18) 0%, rgba(196, 150, 98, 0) 26%),
            radial-gradient(circle at 82% 22%, rgba(255, 176, 92, 0.13) 0%, rgba(255, 176, 92, 0) 24%),
            linear-gradient(135deg, #11161d 0%, #191d24 42%, #12161d 100%);
        font-family: "Tajawal", sans-serif;
    }

    /* Desktop: fixed hero height */
@media (min-width: 768px) {
    .hero-slider-section {
        height: var(--hero-height);
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-slider,
    .hero-slider .slick-list,
    .hero-slider .slick-track {
        height: var(--hero-height) !important;
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-slide-item {
        height: var(--hero-height);
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-slider .slick-slide {
        height: var(--hero-height) !important;
    }
    .hero-slide-item,
    .hero-slide-bg,
    .hero-slide-tint,
    .hero-slide-media,
    .hero-slide-overlay,
    .hero-slide-effects {
        height: var(--hero-height);
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
}

    .hero-slider-section.theme-light {
        background:
            radial-gradient(circle at 18% 18%, rgba(249, 178, 115, 0.16) 0%, rgba(249, 178, 115, 0) 28%),
            radial-gradient(circle at 82% 22%, rgba(120, 167, 255, 0.14) 0%, rgba(120, 167, 255, 0) 26%),
            linear-gradient(135deg, #f6f8fb 0%, #f2f5f9 42%, #eef2f7 100%);
    }
    .hero-slider-section.theme-light .hero-slide-overlay {
        background:
            linear-gradient(90deg, rgba(255, 255, 255, 0.92) 0%, rgba(248, 250, 252, 0.88) 44%, rgba(245, 247, 250, 0.72) 68%, rgba(245, 247, 250, 0.56) 100%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.38) 100%);
    }
    .hero-slider-section.theme-light .hero-slide-title { color: var(--hsp-title-color, #0b1220); }
    .hero-slider-section.theme-light .hero-slide-text { color: var(--hsp-text-color, rgba(17, 24, 39, 0.78)); }
    .hero-slider-section.theme-light .hero-visual-ring { border-color: rgba(120, 136, 160, 0.22); }
    .hero-slider-section.theme-light .hero-arrow {
        border-color: rgba(10, 17, 29, 0.08);
        background: rgba(255, 255, 255, 0.72);
        color: #0b1220;
    }
    .hero-slider-section.theme-light .hero-arrow:hover {
        background: rgba(15, 102, 255, 0.95);
        border-color: rgba(15, 102, 255, 0.95);
        color: #ffffff;
    }
    .hero-slider-section.theme-light .hero-slider .slick-dots li button {
        background: rgba(13, 24, 45, 0.22);
    }
    .hero-slider-section.theme-light .hero-slider .slick-dots li.slick-active button {
        background: #0f66ff;
    }
    .hero-slider-section.is-top-attached {
        margin-top: 0 !important;
        padding-top: 0;
    }
    .hero-slider,
    .hero-slider .slick-list,
    .hero-slider .slick-track {
        height: var(--hero-height) !important;
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-slider .slick-slide {
        min-width: 0;
        height: var(--hero-height) !important;
    }
    /*
     * Fade / zoom / flip: stack every slide in the same viewport.
     * Slick fade + RTL offsets slides with right:-Npx so slides 2+ sit outside .slick-list.
     */
    .hero-slider.slick-initialized:not(.te-slide) .slick-list {
        overflow: hidden !important;
    }
    .hero-slider.slick-initialized:not(.te-slide) .slick-track {
        transform: none !important;
        width: 100% !important;
        height: 100% !important;
    }
    .hero-slider.slick-initialized:not(.te-slide) .slick-slide {
        position: absolute !important;
        left: 0 !important;
        right: 0 !important;
        top: 0 !important;
        width: 100% !important;
        float: none !important;
    }
    .hero-slider.slick-slider:not(.te-slide) .slick-slide.slick-active {
        z-index: 2 !important;
    }
    .hero-slider.slick-slider:not(.te-slide) .slick-slide:not(.slick-active) {
        z-index: 1 !important;
        pointer-events: none;
    }
    .hero-slider .slick-slide > div,
    .hero-slider .slick-slide .hero-slide-item,
    .hero-slider .slick-slide .hero-slide-inner {
        width: 100%;
        height: 100%;
    }
    .hero-slide-item {
        position: relative;
        display: flex !important;
        align-items: center;
        height: var(--hero-height);
        min-height: var(--hero-height);
        max-height: var(--hero-height);
        overflow: hidden;
        isolation: isolate;
        box-sizing: border-box;
    }
    .hero-slide-bg,
    .hero-slide-tint,
    .hero-slide-media,
    .hero-slide-overlay,
    .hero-slide-effects {
        position: absolute;
        inset: 0;
    }
    .hero-slide-bg {
        background-position: center center;
        background-repeat: no-repeat;
        background-size: min(360px, 32vw);
        opacity: 0.14;
        z-index: 0;
        transform: translateZ(0);
    }
    .hero-slider-section.theme-light .hero-slide-bg {
        opacity: 0.1;
    }
    .hero-slide-tint {
        z-index: 1;
        pointer-events: none;
    }
    .hero-slide-media {
        top: 12%;
        right: -3%;
        bottom: 10%;
        left: auto;
        width: min(46vw, 720px);
        background-position: center right;
        background-size: contain;
        opacity: 0.16;
        filter: blur(46px) saturate(1.1);
        transform: translate3d(0, 0, 0) scale(1.04);
        transition: transform 7s ease, opacity 0.45s ease;
        z-index: 0;
    }
    .hero-slider .slick-current .hero-slide-media {
        transform: translate3d(-18px, 0, 0) scale(1.08);
    }
    .hero-slide-overlay {
        background:
            linear-gradient(90deg, rgba(17, 22, 29, 0.97) 0%, rgba(23, 28, 36, 0.92) 44%, rgba(28, 32, 39, 0.72) 68%, rgba(24, 28, 34, 0.52) 100%),
            linear-gradient(180deg, rgba(18, 22, 28, 0.16) 0%, rgba(18, 22, 28, 0.52) 100%);
        z-index: 2;
    }
    .hero-slide-effects {
        z-index: 3;
        overflow: hidden;
        pointer-events: none;
    }
    .hero-plexus {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0.96;
        pointer-events: auto;
    }
    .hero-orb {
        position: absolute;
        border-radius: 999px;
        filter: blur(24px);
        opacity: 0.44;
        animation: heroFloat 18s ease-in-out infinite;
    }
    .hero-orb.orb-1 {
        top: 8%;
        left: -4%;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle, rgba(181, 126, 74, 0.24) 0%, rgba(181, 126, 74, 0) 76%);
    }
    .hero-orb.orb-2 {
        right: -8%;
        top: 18%;
        width: 620px;
        height: 620px;
        background: radial-gradient(circle, rgba(84, 91, 102, 0.24) 0%, rgba(84, 91, 102, 0) 78%);
        animation-duration: 20s;
        animation-delay: -3s;
    }
    .hero-orb.orb-3 {
        bottom: -4%;
        left: 26%;
        width: 360px;
        height: 360px;
        background: radial-gradient(circle, rgba(255, 193, 94, 0.16) 0%, rgba(255, 193, 94, 0) 74%);
        animation-duration: 15s;
        animation-delay: -6s;
    }
    .hero-grid {
        position: absolute;
        inset: -10%;
        background-image:
            repeating-linear-gradient(-58deg, rgba(255, 176, 92, 0.08) 0 12px, transparent 12px 88px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
        background-size: auto, 130px 130px;
        mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.95), transparent 82%);
        opacity: 0.24;
        animation: heroGridMove 26s linear infinite;
    }
    .hero-mesh {
        position: absolute;
        inset: 8% 6%;
        background-image:
            linear-gradient(0deg, transparent 0 47.5%, rgba(129, 138, 150, 0.12) 48%, transparent 48.5%),
            linear-gradient(90deg, transparent 0 47.5%, rgba(129, 138, 150, 0.12) 48%, transparent 48.5%),
            repeating-linear-gradient(0deg, rgba(255, 176, 92, 0.06) 0 2px, transparent 2px 74px);
        background-size: 220px 220px, 220px 220px, 100% 74px;
        background-position: 0 0, 0 0, 0 24px;
        opacity: 0.2;
        mask-image: radial-gradient(circle at center, rgba(0, 0, 0, 0.9), transparent 86%);
        animation: heroMeshMove 30s linear infinite;
    }
    .hero-wave {
        position: absolute;
        border: 1px solid rgba(170, 134, 92, 0.22);
        border-radius: 999px;
        transform: rotate(-14deg);
        opacity: 0.56;
    }
    .hero-wave.wave-1 {
        width: 900px;
        height: 900px;
        right: -250px;
        top: -220px;
        border-color: rgba(149, 163, 184, 0.14);
        animation: heroWaveSpin 36s linear infinite;
    }
    .hero-wave.wave-2 {
        width: 1120px;
        height: 1120px;
        right: -380px;
        top: -330px;
        border-color: rgba(255, 176, 92, 0.18);
        animation: heroWaveSpinReverse 42s linear infinite;
    }
    .hero-slide-item .hero-slide-container {
        position: relative;
        z-index: 3;
        width: 100%;
        height: 100%;
        min-height: 0;
        padding-left: var(--hsp-pad-x, 15px) !important;
        padding-right: var(--hsp-pad-x, 15px) !important;
        box-sizing: border-box;
    }
    .hero-slide-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.28fr) minmax(240px, 0.72fr);
        align-items: center;
        column-gap: var(--hsp-col-gap);
        row-gap: clamp(16px, 2.5vw, 28px);
        height: 100%;
        min-height: 0;
        max-height: 100%;
        width: 100%;
        grid-auto-flow: row dense;
        box-sizing: border-box;
    }
    .hero-slide-content {
        position: relative;
        z-index: 4;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
        gap: var(--hsp-stack-gap);
        min-width: 0;
        width: 100%;
        max-width: min(100%, 760px);
        max-height: 100%;
        overflow: visible;
        padding: var(--hsp-content-pad-y) 0;
        padding-inline-start: var(--hsp-pad-x, 15px);
        padding-inline-end: var(--hsp-pad-x, 15px);
        padding-left: var(--hsp-pad-x, 15px) !important;
        padding-right: var(--hsp-pad-x, 15px) !important;
        box-sizing: border-box;
    }
    .hero-slide-content.is-align-center .hero-slide-title,
    .hero-slide-content.is-align-center .hero-slide-text,
    .hero-slide-content.is-align-center .hero-slide-actions {
        margin-left: auto;
        margin-right: auto;
    }
    .hero-slide-content.is-align-left .hero-slide-title,
    .hero-slide-content.is-align-left .hero-slide-text,
    .hero-slide-content.is-align-left .hero-slide-actions {
        margin-left: 0;
        margin-right: auto;
    }
    .hero-slide-content.is-align-right .hero-slide-title,
    .hero-slide-content.is-align-right .hero-slide-text,
    .hero-slide-content.is-align-right .hero-slide-actions {
        margin-left: auto;
        margin-right: 0;
    }
    .hero-slide-title,
    .hero-slide-text,
    .hero-slide-actions,
    .hero-slide-visual {
        opacity: 1;
        transform: none;
    }
    /*
     * Keep the FIRST slide visible before Slick init so LCP text paints immediately.
     * Hiding the whole slider (visibility:hidden) caused 30s+ LCP render-delay on mobile.
     */
    .hero-slider.has-multiple-slides:not(.slick-initialized) {
        visibility: visible;
        overflow: hidden;
    }
    .hero-slider.has-multiple-slides:not(.slick-initialized) > .hero-slide-item:not(:first-of-type),
    .hero-slider.has-multiple-slides:not(.slick-initialized) > article.hero-slide-item:not(:first-of-type) {
        display: none !important;
    }
    .hero-slider.has-multiple-slides.slick-initialized {
        visibility: visible;
    }
    .hero-slide-title,
    .hero-slide-title .hero-title-desktop,
    .hero-slide-title .hero-title-mobile {
        font-family: "Tajawal", sans-serif !important;
    }
    .hero-slide-title {
        width: 100%;
        max-width: 720px;
        margin: 0;
        color: var(--hsp-title-color, #ffffff);
        font-size: var(--hsp-title-size);
        line-height: 1.25;
        font-weight: 800;
        letter-spacing: -0.02em;
        white-space: normal;
        word-break: normal;
        overflow-wrap: break-word;
    }
    .hero-title-desktop { display: inline; }
    .hero-title-mobile { display: none; }
    @media (max-width: 991px) {
        .hero-title-desktop { display: none; }
        .hero-title-mobile { display: inline; }
    }
    .hero-slide-text {
        width: 100%;
        max-width: 720px;
        margin: 0;
        color: var(--hsp-text-color, rgba(222, 228, 237, 0.76));
        font-size: var(--hsp-text-size);
        line-height: 1.8;
        white-space: pre-line;
        word-break: normal;
        overflow-wrap: break-word;
    }
    .hero-slide-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 14px;
        width: 100%;
        max-width: 720px;
        margin-top: var(--hsp-actions-mt);
    }
    .hero-slide-actions.is-align-center { justify-content: center; }
    .hero-slide-actions.is-align-left { justify-content: left; }
    .hero-slide-actions.is-align-right { justify-content: right; }
    .hero-slide-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 24px;
        max-width: 100%;
        min-height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--hsp-btn1, #0f66ff) 0%, var(--hsp-btn2, #4f46e5) 58%, var(--hsp-btn3, #ff5e14) 100%);
        color: var(--hsp-btn-text, #ffffff);
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        white-space: normal;
        word-break: break-word;
        text-decoration: none;
        box-shadow: 0 14px 32px rgba(18, 48, 120, 0.28);
        transition: transform 0.35s ease, box-shadow 0.35s ease, filter 0.35s ease;
    }
    .hero-slide-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 42px rgba(18, 48, 120, 0.36);
        filter: saturate(1.08);
        color: var(--hsp-btn-text, #ffffff);
    }
    .hero-slide-button-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
        font-size: 15px;
        flex-shrink: 0;
    }
    .hero-slide-visual {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-width: 0;
        min-height: 0;
        max-height: 100%;
        height: 100%;
        padding: var(--hsp-visual-pad-y) 0;
        overflow: visible;
        box-sizing: border-box;
    }
    @media (max-width: 767px) {
        .hero-slide-actions {
            margin-top: 4px;
            gap: 10px;
        }
        .hero-slide-visual {
            padding-top: 4px;
            padding-bottom: 4px;
        }
    }
    .hero-slide-visual.hero-img-anchored {
        align-items: flex-end;
        align-self: stretch;
        padding-bottom: 0;
    }
    .hero-img-anchored .hero-slide-machine {
        border-radius: 28px 28px 0 0;
        -webkit-clip-path: inset(0 round 28px 28px 0 0);
        clip-path: inset(0 round 28px 28px 0 0);
        animation: none;
        max-height: var(--hsp-img-max-h);
        object-position: bottom;
    }
    .hero-slide-visual.hero-img-floating .hero-slide-machine {
        animation: heroMachineFloat 7s ease-in-out infinite;
    }
    .hero-visual-glow,
    .hero-visual-ring {
        position: absolute;
        pointer-events: none;
    }
    .hero-visual-glow.glow-1 {
        width: 420px;
        height: 420px;
        right: 12%;
        top: 26%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(48, 139, 255, 0.34) 0%, rgba(48, 139, 255, 0) 68%);
        filter: blur(16px);
    }
    .hero-visual-glow.glow-2 {
        width: 300px;
        height: 300px;
        right: 20%;
        bottom: 18%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 94, 20, 0.24) 0%, rgba(255, 94, 20, 0) 70%);
        filter: blur(18px);
    }
    .hero-visual-ring.ring-1 {
        width: min(90%, 540px);
        aspect-ratio: 1;
        border-radius: 50%;
        border: 1px solid rgba(119, 142, 174, 0.22);
        right: 2%;
        top: 12%;
    }
    .hero-visual-ring.ring-2 {
        width: min(72%, 420px);
        aspect-ratio: 1;
        border-radius: 50%;
        border: 1px dashed rgba(255, 255, 255, 0.14);
        right: 10%;
        top: 20%;
        animation: heroWaveSpin 24s linear infinite;
    }
    .hero-slide-machine {
        position: relative;
        z-index: 2;
        display: block;
        width: auto;
        max-width: min(100%, calc(var(--hero-height) * 0.58));
        height: auto;
        max-height: var(--hsp-img-max-h);
        object-fit: contain;
        border-radius: 24px;
        -webkit-clip-path: inset(0 round 24px);
        clip-path: inset(0 round 24px);
        filter:
            drop-shadow(0 28px 56px rgba(0, 0, 0, 0.45))
            drop-shadow(0 0 36px rgba(59, 130, 246, 0.14));
        transform: translate3d(0, 0, 0);
    }
    .elementor-widget-container > .hero-slider-section,
    .elementor-element > .elementor-widget-container > .hero-slider-section,
    .elementor-widget-shortcode .hero-slider-section,
    .elementor-widget-text-editor .hero-slider-section,
    .elementor-widget-html .hero-slider-section,
    .e-con .hero-slider-section,
    .e-con-inner .hero-slider-section {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        left: auto !important;
        right: auto !important;
        transform: none !important;
        float: none !important;
        position: relative !important;
        box-sizing: border-box;
    }
    .elementor-widget-container:has(> .hero-slider-section),
    .e-con:has(.hero-slider-section) {
        overflow: hidden;
        max-width: 100%;
    }
    .hero-slider .slick-dots {
        position: absolute;
        right: auto;
        bottom: 42px;
        left: 50px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        width: auto;
        margin: 0;
        padding: 0;
        list-style: none;
        z-index: 10;
        pointer-events: auto;
    }
    .hero-slider .slick-dots li,
    .hero-slider .slick-dots li button {
        pointer-events: auto;
        cursor: pointer;
    }
    .hero-slider .slick-dots li {
        width: auto;
        height: auto;
        margin: 0;
    }
    .hero-slider .slick-dots li button {
        width: 14px;
        height: 14px;
        padding: 0;
        border: none;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.22);
        font-size: 0;
        transition: all 0.35s ease;
    }
    .hero-slider .slick-dots li.slick-active button {
        width: 44px;
        background: #ff5e14;
    }
    .hero-arrow {
        position: absolute;
        top: auto;
        bottom: 34px;
        z-index: 12;
        width: 56px;
        height: 56px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        color: #ffffff;
        pointer-events: auto;
        cursor: pointer;
        transform: none;
        transition: all 0.35s ease;
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(10px);
    }
    .hero-arrow:hover {
        background: rgba(255, 94, 20, 0.9);
        border-color: rgba(255, 94, 20, 0.9);
        color: #ffffff;
    }
    .hero-arrow.hero-prev {
        right: 120px;
        left: auto;
    }
    .hero-arrow.hero-next {
        right: 50px;
    }
    .hero-arrow span {
        font-size: 22px;
        line-height: 1;
    }
    .hero-slider-empty {
        padding: 20px;
        background: #f8f9fa;
        color: #1f2937;
        border-radius: 12px;
    }
    @keyframes heroFloat {
        0% {
            transform: translate3d(0, 0, 0) scale(1);
        }
        50% {
            transform: translate3d(14px, -18px, 0) scale(1.04);
        }
        100% {
            transform: translate3d(0, 0, 0) scale(1);
        }
    }
    @keyframes heroGridMove {
        0% {
            transform: translate3d(0, 0, 0);
        }
        100% {
            transform: translate3d(-20px, -52px, 0);
        }
    }
    @keyframes heroMeshMove {
        0% {
            transform: translate3d(0, 0, 0);
        }
        100% {
            transform: translate3d(0, -36px, 0);
        }
    }
    @keyframes heroWaveSpin {
        0% {
            transform: rotate(-10deg);
        }
        100% {
            transform: rotate(350deg);
        }
    }
    @keyframes heroWaveSpinReverse {
        0% {
            transform: rotate(8deg);
        }
        100% {
            transform: rotate(-352deg);
        }
    }
    @keyframes heroMachineFloat {
        0% {
            transform: translate3d(0, 0, 0);
        }
        50% {
            transform: translate3d(-8px, -14px, 0);
        }
        100% {
            transform: translate3d(0, 0, 0);
        }
    }
    @media (max-width: 1199px) {
        .hero-slide-layout {
            grid-template-columns: minmax(0, 1.2fr) minmax(220px, 0.8fr);
            column-gap: clamp(12px, 2vw, 24px);
        }
        .hero-slide-content {
            max-width: 680px;
        }
        .hero-arrow.hero-prev {
            right: 92px;
            left: auto;
        }
        .hero-arrow.hero-next {
            right: 24px;
        }
        .hero-slide-machine {
            max-width: min(100%, calc(var(--hero-height) * 0.52));
        }
    }
    @media (max-width: 991px) {
        .hero-slider-section {
            --hsp-pad-x: 20px !important;
            --hsp-img-max-h: calc(var(--hero-height) * 0.42);
            --hsp-title-size: clamp(18px, calc(var(--hero-height) * 0.048), 42px);
            --hsp-text-size: clamp(13px, calc(var(--hero-height) * 0.02), 16px);
            --hsp-col-gap: 12px;
            --hsp-stack-gap: 12px;
        }
        .hero-slide-layout {
            grid-template-columns: 1fr;
            grid-template-rows: minmax(0, 1fr) minmax(0, auto);
            gap: 12px;
            align-content: center;
        }
        .hero-slide-content {
            max-width: 100%;
            align-items: center !important;
            padding: var(--hsp-content-pad-y) 0 calc(var(--hsp-content-pad-y) * 0.45);
            padding-inline: var(--hsp-pad-x, 15px);
            text-align: center !important;
        }
        .hero-slide-title,
        .hero-slide-text {
            max-width: 680px;
            margin-inline: auto;
            text-align: center !important;
        }
        .hero-slide-actions {
            justify-content: center !important;
            margin-inline: auto;
        }
        .hero-slide-visual {
            min-height: 0;
            max-height: calc(var(--hero-height) * 0.45);
            padding: calc(var(--hsp-visual-pad-y) * 0.5) 0 calc(var(--hsp-visual-pad-y) * 0.35);
        }
        .hero-visual-ring.ring-1 {
            right: auto;
            left: 50%;
            margin-left: -270px;
        }
        .hero-visual-ring.ring-2 {
            right: auto;
            left: 50%;
            margin-left: -210px;
        }
        .hero-slide-media {
            right: 50%;
            width: min(72vw, 520px);
            opacity: 0.14;
            transform: translateX(50%) scale(1.04);
        }
        .hero-slider .slick-current .hero-slide-media {
            transform: translateX(50%) scale(1.08);
        }
        .hero-slide-machine {
            max-width: min(100%, calc(var(--hero-height) * 0.55));
            max-height: calc(var(--hero-height) * 0.4);
        }
        .hero-slider .slick-dots {
            left: 50%;
            bottom: 28px;
            transform: translateX(-50%);
            justify-content: center;
        }
        .hero-arrow {
            width: 50px;
            height: 50px;
        }
    }

    @media (max-width: 767px) {
        .hero-slider-section {
            --hsp-mobile-height: var(--hsp-mobile-height, var(--hero-height));
            --hsp-img-max-h: calc(var(--hsp-mobile-height) * 0.38);
            --hsp-title-size: clamp(17px, calc(var(--hsp-mobile-height) * 0.044), 34px);
            --hsp-text-size: clamp(12px, calc(var(--hsp-mobile-height) * 0.019), 15px);
            height: var(--hsp-mobile-height) !important;
            min-height: var(--hsp-mobile-height) !important;
            max-height: var(--hsp-mobile-height) !important;
            contain: none;
        }
        .hero-slider,
        .hero-slider .slick-list,
        .hero-slider .slick-track {
            height: var(--hsp-mobile-height) !important;
            min-height: var(--hsp-mobile-height) !important;
            max-height: var(--hsp-mobile-height) !important;
        }
        .hero-slider .slick-slide {
            height: var(--hsp-mobile-height) !important;
            min-height: var(--hsp-mobile-height) !important;
            max-height: var(--hsp-mobile-height) !important;
        }
        .hero-slider .slick-slide > div,
        .hero-slider .slick-slide .hero-slide-item,
        .hero-slider .slick-slide .hero-slide-inner {
            height: 100% !important;
            min-height: var(--hsp-mobile-height) !important;
        }
        .hero-slide-item {
            height: var(--hsp-mobile-height) !important;
            min-height: var(--hsp-mobile-height) !important;
            max-height: var(--hsp-mobile-height) !important;
            align-items: center;
        }
        .hero-slide-bg,
        .hero-slide-tint,
        .hero-slide-media,
        .hero-slide-overlay,
        .hero-slide-effects {
            height: var(--hsp-mobile-height) !important;
            min-height: var(--hsp-mobile-height) !important;
            max-height: var(--hsp-mobile-height) !important;
        }
        .hero-slide-bg {
            background-size: min(240px, 56vw);
            opacity: 0.1;
        }
        .hero-slide-layout {
            grid-template-rows: auto auto;
            align-content: start;
        }
        .hero-slide-content {
            max-width: 100%;
            width: 100%;
            padding: calc(var(--hsp-content-pad-y) * 0.85) 0 8px;
            padding-inline: var(--hsp-pad-x, 15px);
        }
        .hero-slide-title {
            width: 100%;
            max-width: 100%;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        .hero-slide-text {
            width: 100%;
            max-width: 100%;
        }
        .hero-slide-actions {
            margin-top: calc(var(--hsp-actions-mt) * 0.85);
        }
        .hero-slide-button {
            min-height: clamp(44px, calc(var(--hero-height) * 0.07), 50px);
            padding: clamp(10px, calc(var(--hero-height) * 0.018), 14px) clamp(14px, calc(var(--hero-height) * 0.028), 20px);
            font-size: clamp(12px, calc(var(--hero-height) * 0.019), 14px);
        }
        .hero-slide-visual {
            min-height: 0;
            max-height: none;
            height: auto;
            background: transparent;
            padding: 4px 0 calc(var(--hsp-visual-pad-y) * 0.35);
        }
        .hero-slide-visual .hero-visual-glow,
        .hero-slide-visual .hero-visual-ring {
            display: none !important;
        }
        .hero-slide-media {
            display: none !important;
        }
        .hero-slide-machine {
            max-width: min(100%, calc(var(--hsp-mobile-height) * 0.72));
            max-height: calc(var(--hsp-mobile-height) * 0.36);
            border-radius: 0;
            -webkit-clip-path: none;
            clip-path: none;
            background: transparent;
            filter: none;
        }
        .hero-img-anchored .hero-slide-machine {
            border-radius: 0;
            -webkit-clip-path: none;
            clip-path: none;
        }
        .hero-slider .slick-dots {
            right: auto;
            bottom: 24px;
            left: 50%;
            gap: 10px;
        }
        .hero-arrow {
            bottom: 18px;
            width: 42px;
            height: 42px;
        }
        .hero-arrow.hero-prev {
            right: 68px;
            left: auto;
        }
        .hero-arrow.hero-next {
            right: 20px;
        }
        .hero-orb.orb-1 {
            width: 280px;
            height: 280px;
        }
        .hero-orb.orb-2 {
            width: 380px;
            height: 380px;
        }
        .hero-wave.wave-1 {
            width: 620px;
            height: 620px;
            right: -240px;
        }
        .hero-wave.wave-2 {
            width: 760px;
            height: 760px;
            right: -320px;
        }
    }

    /* Transition Effects on Slide Change */
    .hero-slider.te-zoom .hero-slide-item,
    .hero-slider.te-flip .hero-slide-item,
    .hero-slider.te-slide .hero-slide-item {
        /* will-change causes Firefox to discard layers while off-screen → black flash */
        will-change: auto;
        transition: transform var(--hsp-anim-speed, 600ms) var(--hsp-anim-ease, ease),
                    opacity var(--hsp-anim-speed, 600ms) var(--hsp-anim-ease, ease),
                    filter var(--hsp-anim-speed, 600ms) var(--hsp-anim-ease, ease);
    }

    /* Firefox: keep slider layers painted when scrolling away/back */
    @supports (-moz-appearance: none) {
        .hero-slider-section,
        .hero-slider,
        .hero-slider .slick-list,
        .hero-slider .slick-track,
        .hero-slide-item,
        .hero-slide-effects,
        .hero-plexus {
            transform: translateZ(0);
            backface-visibility: hidden;
            -moz-backface-visibility: hidden;
        }
        .hero-slider.has-multiple-slides:not(.slick-initialized) {
            visibility: visible;
            opacity: 1;
        }
        .hero-slider .slick-slide {
            -moz-force-broken-image-icon: 0;
        }
        .hero-slide-effects,
        .hero-plexus {
            /* Prevent canvas from being discarded as an empty transparent layer */
            background-color: transparent;
            opacity: 0.999;
        }
    }

    /* Zoom & Fade */
    .hero-slider.te-zoom .hero-slide-item.is-enter { transform: scale(1); filter: blur(0); }
    .hero-slider.te-zoom .hero-slide-item.is-exit { transform: scale(1.06); filter: blur(1.2px); }

    /* Flip (3D) */
    .hero-slider.te-flip { perspective: 1200px; transform-style: preserve-3d; }
    .hero-slider.te-flip .hero-slide-item { backface-visibility: hidden; transform-origin: 50% 50%; }
    .hero-slider.te-flip .hero-slide-item.is-enter { transform: rotateY(0deg) scale(1); filter: none; }
    .hero-slider.te-flip .hero-slide-item.is-exit { transform: rotateY(-12deg) scale(0.985); filter: blur(0.6px); }

    /* Slide transition: parallax on active slide content only */
    .hero-slider.te-slide .slick-active .hero-slide-content {
        transform: translateX(0);
        opacity: 1;
        transition: transform var(--hsp-anim-speed, 600ms) var(--hsp-anim-ease, ease), opacity var(--hsp-anim-speed, 600ms) var(--hsp-anim-ease, ease);
    }

    @media (max-width: 575px) {
        .hero-slider-section {
            --hsp-img-max-h: calc(var(--hsp-mobile-height, var(--hero-height)) * 0.34);
            --hsp-title-size: clamp(16px, calc(var(--hsp-mobile-height, var(--hero-height)) * 0.04), 28px);
        }
        .hero-slide-content {
            padding: calc(var(--hsp-content-pad-y) * 0.75) 0 6px;
            padding-inline: var(--hsp-pad-x, 15px);
        }
        .hero-slide-title {
            line-height: 1.3;
        }
        .hero-slide-button {
            width: 100%;
            max-width: 280px;
        }
        .hero-slide-button-icon {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }
        .hero-slide-visual {
            min-height: 0;
            max-height: none;
            height: auto;
            padding: 4px 0 calc(var(--hsp-visual-pad-y) * 0.3);
        }
        .hero-slide-machine {
            max-width: min(100%, calc(var(--hsp-mobile-height, var(--hero-height)) * 0.78));
            max-height: calc(var(--hsp-mobile-height, var(--hero-height)) * 0.32);
            border-radius: 0;
            -webkit-clip-path: none;
            clip-path: none;
            filter: none;
        }
        .hero-slide-media {
            width: min(88vw, 360px);
        }
        .hero-slider .slick-dots li button {
            width: 12px;
            height: 12px;
        }
        .hero-slider .slick-dots li.slick-active button {
            width: 34px;
        }
    }
    ';


    // ══════════════════════════════════════════════════════════════════
    // إعدادات الصورة البارزة على الشاشات الصغيرة
    // Mobile Featured Image Visibility CSS
    // ══════════════════════════════════════════════════════════════════
    $css .= '
    /* ================================================================
       Featured Image Mobile Visibility
       data-img-mobile="show"    → ظاهرة على جميع الشاشات
       data-img-mobile="hide"    → مخفية على الشاشات < 768px
       data-img-mobile="hide_xs" → مخفية على الشاشات < 576px فقط
    ================================================================ */

    /* ── show: إظهار الصورة على الموبايل ── */
    @media (max-width: 991px) {
        .hero-slide-item[data-img-mobile="show"] .hero-slide-visual {
            display: flex !important;
            min-height: 0;
            max-height: calc(var(--hero-height) * 0.45);
        }
        .hero-slide-item[data-img-mobile="show"] .hero-slide-machine {
            display: block !important;
        }
    }
    @media (max-width: 767px) {
        .hero-slide-item[data-img-mobile="show"] .hero-slide-visual {
            max-height: none;
            height: auto;
        }
    }

    /* ── hide: إخفاء الصورة على الشاشات < 768px ── */
    @media (max-width: 767px) {
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-visual,
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-machine,
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-media,
        .hero-slide-item[data-img-mobile="hide"] .hero-visual-glow,
        .hero-slide-item[data-img-mobile="hide"] .hero-visual-ring {
            display: none !important;
        }
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-layout {
            grid-template-columns: 1fr !important;
            grid-template-rows: auto !important;
            align-items: center !important;
            align-content: center !important;
        }
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-content {
            max-width: 100% !important;
            padding-top: var(--hsp-content-pad-y) !important;
            padding-bottom: calc(var(--hsp-content-pad-y) + 52px) !important;
            text-align: center;
        }
        .hero-slide-item[data-img-mobile="hide"] .hero-slide-actions {
            justify-content: center;
        }
    }

    /* ── hide_xs: إظهار بين 576-767px، إخفاء فقط < 576px ── */
    @media (min-width: 576px) and (max-width: 767px) {
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-visual {
            display: flex !important;
        }
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-machine {
            display: block !important;
        }
    }
    @media (max-width: 575px) {
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-visual,
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-machine,
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-media,
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-visual-glow,
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-visual-ring {
            display: none !important;
        }
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-layout {
            grid-template-columns: 1fr !important;
            grid-template-rows: auto !important;
            align-items: center !important;
            align-content: center !important;
        }
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-content {
            max-width: 100% !important;
            padding-top: var(--hsp-content-pad-y) !important;
            padding-bottom: calc(var(--hsp-content-pad-y) + 52px) !important;
            text-align: center;
        }
        .hero-slide-item[data-img-mobile="hide_xs"] .hero-slide-actions {
            justify-content: center;
        }
    }
    ';

    /* Split Layout Style */
    $css .= '
    /* ================================================================
       Split Layout Style: half text (solid bg) / half image (tinted)
    ================================================================ */
    .hero-slider-section.slide-style-split .hero-slide-overlay,
    .hero-slider-section.slide-style-split .hero-slide-tint { display: none !important; }
    .hero-slider-section.slide-style-split .hero-slide-item .hero-slide-container { padding-left: 0 !important; padding-right: 0 !important; }
    .hero-slider-section.slide-style-split .hero-slide-item::before,
    .hero-slider-section.slide-style-split .hero-slide-item::after {
        content: "";
        position: absolute;
        top: 0; bottom: 0;
        width: 50%;
        pointer-events: none;
    }
    /* Left side background for text */
    .hero-slider-section.slide-style-split .hero-slide-item::before {
        left: 0;
        background: var(--hsp-split-bg, #ffffff);
        z-index: 1;
    }
    /* Right side tint over image */
    .hero-slider-section.slide-style-split .hero-slide-item::after {
        right: 0;
        background: var(--hsp-split-tint, rgba(44,100,100,0.55));
        z-index: 3;
    }
    /* Equal halves layout */
    .hero-slider-section.slide-style-split .hero-slide-layout {
        grid-template-columns: 1fr 1fr;
        gap: 0;
        height: 100%;
        min-height: 0;
        max-height: 100%;
    }
    .hero-slider-section.slide-style-split .hero-slide-content,
    .hero-slider-section.slide-style-split .hero-slide-visual {
        position: relative;
        z-index: 4;
        max-height: 100%;
        overflow: hidden;
        padding-top: var(--hsp-content-pad-y);
        padding-bottom: calc(var(--hsp-content-pad-y) * 0.75);
        box-sizing: border-box;
    }
    .hero-slider-section.slide-style-split .hero-slide-machine {
        max-height: var(--hsp-img-max-h);
        max-width: min(100%, calc(var(--hero-height) * 0.48));
    }
    .hero-slider-section.slide-style-split .hero-slide-content { max-width: 720px; }
    .hero-slider-section.slide-style-split .hero-slide-visual { align-items: center; justify-content: center; }
    /* Clean look: hide decorative background effects */
    .hero-slider-section.slide-style-split .hero-slide-media,
    .hero-slider-section.slide-style-split .hero-visual-ring,
    .hero-slider-section.slide-style-split .hero-visual-glow,
    .hero-slider-section.slide-style-split .hero-grid,
    .hero-slider-section.slide-style-split .hero-mesh,
    .hero-slider-section.slide-style-split .hero-orb,
    .hero-slider-section.slide-style-split .hero-wave { display: none !important; }
    /* Image Centered: full-quality background only, no overlay, no featured image */
    .hero-slider-section.slide-style-image_centered .hero-slide-overlay,
    .hero-slider-section.slide-style-image_centered .hero-slide-tint,
    .hero-slider-section.slide-style-image_centered .hero-slide-media,
    .hero-slider-section.slide-style-image_centered .hero-slide-visual {
        display: none !important;
    }
    .hero-slider-section.slide-style-image_centered .hero-slide-bg {
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        filter: none;
        transform: none;
        opacity: 1;
    }
    .hero-slider-section.slide-style-image_centered .hero-slide-layout {
        grid-template-columns: 1fr;
        justify-items: center;
        align-items: center;
        text-align: center;
    }
    .hero-slider-section.slide-style-image_centered .hero-slide-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        max-width: min(100%, 820px);
        margin: 0 auto;
        text-align: center;
        padding-inline: var(--hsp-pad-x, 15px);
    }
    .hero-slider-section.slide-style-image_centered .hero-slide-title,
    .hero-slider-section.slide-style-image_centered .hero-slide-text {
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
    }
    .hero-slider-section.slide-style-image_centered .hero-slide-actions {
        justify-content: center;
    }
    .hero-slider-section.slide-style-image_centered {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    @media (max-width: 991px) {
        .hero-slider-section.slide-style-split .hero-slide-item::before,
        .hero-slider-section.slide-style-split .hero-slide-item::after { display: none; }
        .hero-slider-section.slide-style-split .hero-slide-layout { grid-template-columns: 1fr; }
        .hero-slider-section.slide-style-split .hero-slide-content { padding-inline: var(--hsp-pad-x, 15px); text-align: center; }
        .hero-slider-section.slide-style-split .hero-slide-visual { padding: 10px 0 92px; }
    }

    /* Elementor / theme isolation — styles apply only inside the slider */
    .hero-slider-section,
    .hero-slider-section * {
        box-sizing: border-box;
    }
    .hero-slider-section .hero-slider,
    .hero-slider-section .hero-slider .slick-list,
    .hero-slider-section .hero-slider .slick-track {
        max-width: 100%;
        overflow: hidden;
    }
    .hero-slider-section .hero-arrow,
    .hero-slider-section .hero-slider .slick-dots {
        position: absolute;
        z-index: 5;
    }
    .hero-slider-section img,
    .hero-slider-section canvas,
    .hero-slider-section button {
        max-width: 100%;
    }

    /* ═══ Portal Layout (LVN-style hero + cards) ═══ */
    .hero-slider-section.is-portal-layout,
    .hero-slider-section.slide-style-portal {
        background: transparent;
    }
    .hero-slider-section.is-portal-layout .hero-slide-effects,
    .hero-slider-section.slide-style-portal .hero-slide-effects {
        display: none !important;
    }
    .hero-portal-slide {
        display: flex !important;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        position: relative;
        overflow: hidden;
        height: var(--hero-height) !important;
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-portal-base {
        position: absolute;
        inset: 0;
        z-index: 0;
        background: var(--hsp-portal-bg, radial-gradient(ellipse at center, #6b21a8 0%, #1e3a5f 100%));
    }
    .hero-portal-slide--has-image .hero-portal-base {
        background: #0f172a;
    }
    .hero-portal-photo {
        position: absolute;
        inset: 0;
        z-index: 1;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        pointer-events: none;
    }
    .hero-portal-tint {
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
    }
    .hero-portal-slide--has-image .hero-portal-inner {
        z-index: 3;
    }
    .hero-portal-pattern {
        position: absolute;
        inset: 0;
        z-index: 1;
        opacity: var(--hsp-portal-pattern-opacity, 0.35);
        mix-blend-mode: var(--hsp-portal-pattern-blend, overlay);
        background-image: var(--hsp-portal-pattern-bg);
        background-size: var(--hsp-portal-pattern-size, 100%);
        background-repeat: var(--hsp-portal-pattern-repeat, repeat);
        background-position: var(--hsp-portal-pattern-position, center center);
        pointer-events: none;
        transform-origin: center center;
        will-change: transform, background-position, opacity;
    }
    @keyframes hsp-portal-pattern-drift {
        0% { background-position: 0% 0%; }
        50% { background-position: 100% 100%; }
        100% { background-position: 0% 0%; }
    }
    @keyframes hsp-portal-pattern-scroll-h {
        0% { background-position: 0 0; }
        100% { background-position: 280px 0; }
    }
    @keyframes hsp-portal-pattern-scroll-v {
        0% { background-position: 0 0; }
        100% { background-position: 0 280px; }
    }
    @keyframes hsp-portal-pattern-pulse {
        0%, 100% { opacity: calc(var(--hsp-portal-pattern-opacity, 0.35) * 0.55); }
        50% { opacity: var(--hsp-portal-pattern-opacity, 0.35); }
    }
    @keyframes hsp-portal-pattern-zoom {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.14); }
    }
    @keyframes hsp-portal-pattern-rotate {
        from { transform: rotate(0deg) scale(1.15); }
        to { transform: rotate(360deg) scale(1.15); }
    }
    @keyframes hsp-portal-pattern-float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(2%, -1.5%) scale(1.05); }
        66% { transform: translate(-1.5%, 2%) scale(1.03); }
    }
    .hero-slider-section.portal-pattern-anim-drift .hero-portal-pattern {
        animation: hsp-portal-pattern-drift var(--hsp-portal-pattern-anim-duration, 45s) ease-in-out infinite;
    }
    .hero-slider-section.portal-pattern-anim-scroll_h .hero-portal-pattern {
        animation: hsp-portal-pattern-scroll-h var(--hsp-portal-pattern-anim-duration, 45s) linear infinite;
    }
    .hero-slider-section.portal-pattern-anim-scroll_v .hero-portal-pattern {
        animation: hsp-portal-pattern-scroll-v var(--hsp-portal-pattern-anim-duration, 45s) linear infinite;
    }
    .hero-slider-section.portal-pattern-anim-pulse .hero-portal-pattern {
        animation: hsp-portal-pattern-pulse var(--hsp-portal-pattern-anim-duration, 45s) ease-in-out infinite;
    }
    .hero-slider-section.portal-pattern-anim-zoom .hero-portal-pattern {
        animation: hsp-portal-pattern-zoom var(--hsp-portal-pattern-anim-duration, 45s) ease-in-out infinite;
    }
    .hero-slider-section.portal-pattern-anim-rotate .hero-portal-pattern {
        animation: hsp-portal-pattern-rotate var(--hsp-portal-pattern-anim-duration, 45s) linear infinite;
    }
    .hero-slider-section.portal-pattern-anim-float .hero-portal-pattern {
        animation: hsp-portal-pattern-float var(--hsp-portal-pattern-anim-duration, 45s) ease-in-out infinite;
    }
    @media (prefers-reduced-motion: reduce) {
        .hero-slider-section.is-portal-layout .hero-portal-pattern {
            animation: none !important;
            transform: none !important;
        }
    }
    .hero-slider-section.is-portal-layout .hero-slider.is-static-portal {
        overflow: hidden;
        height: var(--hero-height) !important;
        min-height: var(--hero-height);
        max-height: var(--hero-height);
    }
    .hero-slider-section.is-portal-layout .hero-slider.is-static-portal .hero-portal-slide {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        transform: none !important;
        width: 100%;
        position: relative;
    }
    .hero-slider-section.is-portal-layout .hero-slider.is-static-portal .hero-arrow,
    .hero-slider-section.is-portal-layout .hero-slider.is-static-portal .slick-dots {
        display: none !important;
    }
    .hero-portal-inner {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 0;
        width: 100%;
        padding: clamp(24px, 4vw, 48px) var(--hsp-portal-cards-pad-x, 32px) var(--hsp-portal-cards-pad-bottom, 48px);
        box-sizing: border-box;
    }
    .hero-portal-hero {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        max-width: min(920px, 100%);
        margin: 0 auto;
        padding: clamp(16px, 3vw, 40px) 0;
    }
    .hero-portal-title {
        margin: 0 0 clamp(12px, 2vw, 24px);
        font-family: var(--hsp-portal-title-font, "Tajawal", sans-serif);
        font-size: clamp(22px, 3.2vw, var(--hsp-portal-title-size, 32px));
        font-weight: var(--hsp-portal-title-weight, 700);
        line-height: var(--hsp-portal-title-line-height, 1.35);
        color: var(--hsp-portal-title-color, #ffffff);
        max-width: 100%;
    }
    .hero-portal-text {
        margin: 0;
        font-family: var(--hsp-portal-text-font, "Tajawal", sans-serif);
        font-size: clamp(14px, 1.8vw, var(--hsp-portal-text-size, 18px));
        font-weight: var(--hsp-portal-text-weight, 400);
        line-height: var(--hsp-portal-text-line-height, 1.6);
        color: var(--hsp-portal-text-color, #ffffff);
        max-width: 100%;
        opacity: 0.95;
    }
    .hero-portal-cards {
        flex: 0 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
        gap: var(--hsp-portal-cards-gap, 24px);
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }
    .hero-portal-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        background: var(--hsp-portal-card-bg, #ffffff);
        border-radius: var(--hsp-portal-card-radius, 16px);
        padding: var(--hsp-portal-card-padding, 24px);
        box-shadow: var(--hsp-portal-card-shadow, 0 8px 32px rgba(0,0,0,0.12));
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hero-portal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.16);
    }
    .hero-portal-card-icon {
        flex: 0 0 auto;
        width: 52px;
        height: 52px;
        border-radius: var(--hsp-portal-card-icon-radius, 12px);
        background: var(--hsp-portal-card-icon-bg, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .hero-portal-card-icon img {
        width: 28px;
        height: 28px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }
    .hero-portal-card-icon--placeholder {
        min-width: 52px;
    }
    .hero-portal-card-body {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .hero-portal-card-title {
        margin: 0;
        font-family: var(--hsp-portal-title-font, "Tajawal", sans-serif);
        font-size: var(--hsp-portal-card-title-size, 18px);
        font-weight: 700;
        color: var(--hsp-portal-card-title-color, #111827);
        line-height: 1.3;
    }
    .hero-portal-card-desc {
        margin: 0;
        font-size: var(--hsp-portal-card-desc-size, 14px);
        color: var(--hsp-portal-card-desc-color, #6b7280);
        line-height: 1.55;
    }
    .hero-portal-card-cta {
        align-self: flex-end;
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        font-weight: 600;
        color: var(--hsp-portal-card-cta-color, #3b82f6);
        text-decoration: none;
        transition: color 0.2s ease, gap 0.2s ease;
    }
    .hero-portal-card-cta:hover {
        color: var(--hsp-portal-card-cta-hover, #2563eb);
        gap: 8px;
    }
    .hero-portal-card-cta-arrow {
        font-size: 18px;
        line-height: 1;
    }
    @media (max-width: 767px) {
        .hero-portal-inner {
            padding-bottom: calc(var(--hsp-portal-cards-pad-bottom, 48px) * 0.75);
        }
        .hero-portal-cards {
            grid-template-columns: 1fr;
        }
        .hero-portal-card {
            flex-direction: row;
        }
        .hero-portal-hero {
            padding-top: 8px;
            padding-bottom: 20px;
        }
    }
    ';

    wp_add_inline_style( 'hero-slider-plugin', $css );
}
function hero_slider_enqueue_tajwal_font() {
    if ( ! hero_slider_plugin_is_shortcode_used() ) {
        return;
    }

    // Prefer local Tajawal (already in local-fonts.css) — avoid Google Fonts RTT.
    if ( wp_style_is( 'local-site-fonts', 'registered' ) || wp_style_is( 'local-site-fonts', 'enqueued' ) || wp_style_is( 'local-fonts', 'enqueued' ) ) {
        return;
    }

    wp_enqueue_style(
        'hero-slider-tajawal',
        content_url( '/uploads/local-fonts/local-fonts.css' ),
        array(),
        HERO_SLIDER_PLUGIN_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'hero_slider_plugin_detect_shortcode_in_content', 20 );
function hero_slider_plugin_detect_shortcode_in_content() {
    if ( is_admin() || hero_slider_plugin_is_shortcode_used() ) {
        return;
    }

    global $post;
    if ( ! $post instanceof WP_Post ) {
        return;
    }

    if ( has_shortcode( $post->post_content, 'hero_slider' ) ) {
        hero_slider_plugin_mark_shortcode_used();
        hero_slider_enqueue_tajwal_font();
        hero_slider_plugin_styles();
    }
}

add_action( 'wp_enqueue_scripts', 'hero_slider_enqueue_tajwal_font', 99 );
add_action( 'wp_enqueue_scripts', 'hero_slider_plugin_styles', 100 );
