<?php
$genres = [
    'text' => ['label' => 'テキスト', 'icon' => 'type'],
    'button' => ['label' => 'ボタン', 'icon' => 'mouse-pointer-click'],
    'card' => ['label' => 'カード', 'icon' => 'panel-top'],
    'layout' => ['label' => 'レイアウト', 'icon' => 'columns-3'],
];
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tailwind Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [v-cloak] { display: none; }
        * { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        input[type="range"] { accent-color: #0f766e; }
        input[type="color"] { appearance: none; border: 0; padding: 0; background: none; }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: 0; border-radius: 4px; }
        .checkerboard {
            background-color: #f8fafc;
            background-image: linear-gradient(45deg, #e2e8f0 25%, transparent 25%), linear-gradient(-45deg, #e2e8f0 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e2e8f0 75%), linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0;
        }
        .control:focus { outline: 2px solid #14b8a6; outline-offset: 1px; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<div id="app" v-cloak class="min-h-screen">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-[1480px] flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-md bg-teal-700 text-white shadow-sm">
                    <i data-lucide="wind" class="h-5 w-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Tailwind Studio</h1>
                    <p class="text-xs text-slate-500">見ながらつくる、Utility Class Builder</p>
                </div>
            </div>
            <nav class="flex gap-1 overflow-x-auto rounded-md bg-slate-100 p-1" aria-label="編集ジャンル">
                <?php foreach ($genres as $key => $genre): ?>
                    <button type="button" @click="selectGenre('<?= $key ?>')"
                            :class="genre === '<?= $key ?>' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                            class="flex min-w-max items-center gap-2 rounded px-3 py-2 text-sm font-semibold transition">
                        <i data-lucide="<?= $genre['icon'] ?>" class="h-4 w-4"></i>
                        <?= $genre['label'] ?>
                    </button>
                <?php endforeach; ?>
            </nav>
            <button type="button" @click="resetCurrent" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" title="現在の設定をリセット">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                リセット
            </button>
        </div>
    </header>

    <main class="mx-auto max-w-[1480px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
            <div>
                <p class="text-xs font-bold uppercase text-teal-700">{{ genreLabel }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ genreTitle }}</h2>
            </div>
            <div class="flex rounded-md border border-slate-200 bg-white p-1" aria-label="プレビュー幅">
                <button v-for="item in viewports" :key="item.id" type="button" @click="viewport = item.id"
                        :class="viewport === item.id ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900'"
                        class="grid h-8 w-9 place-items-center rounded transition" :title="item.label">
                    <i :data-lucide="item.icon" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[360px_minmax(0,1fr)]">
            <aside class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold"><i data-lucide="sliders-horizontal" class="h-4 w-4 text-teal-700"></i>詳細設定</h3>
                </div>
                <div class="max-h-[680px] space-y-5 overflow-y-auto p-5">
                    <template v-if="genre === 'text'">
                        <field-label label="表示テキスト">
                            <textarea v-model="settings.text.content" rows="3" class="control w-full resize-none rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </field-label>
                        <select-field label="フォント" v-model="settings.text.family" :options="options.fontFamily"></select-field>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold">文字サイズ</span>
                                <select v-model="settings.text.sizeMode" class="control rounded-md border border-slate-300 bg-white px-2 py-1 text-xs">
                                    <option value="preset">プリセット</option>
                                    <option value="custom">数値指定</option>
                                </select>
                            </div>
                            <select v-if="settings.text.sizeMode === 'preset'" v-model="settings.text.size" class="control w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                <option v-for="item in options.textSize" :key="item[0]" :value="item[0]">{{ item[1] }}</option>
                            </select>
                            <div v-else class="grid grid-cols-[minmax(0,1fr)_100px] gap-2">
                                <input v-model.number="settings.text.sizeValue" type="number" min="0.1" max="500" step="0.1" aria-label="文字サイズの数値" class="control min-w-0 rounded-md border border-slate-300 px-3 py-2 text-sm">
                                <select v-model="settings.text.sizeUnit" aria-label="文字サイズの単位" class="control rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                    <option v-for="unit in options.sizeUnits" :key="unit" :value="unit">{{ unit }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="配置" v-model="settings.text.align" :options="options.align"></select-field>
                            <select-field label="太さの指定" v-model="settings.text.weightMode" :options="options.valueMode"></select-field>
                        </div>
                        <select-field v-if="settings.text.weightMode === 'preset'" label="太さ" v-model="settings.text.weight" :options="options.weight"></select-field>
                        <field-label v-else label="太さ（100〜900）">
                            <input v-model.number="settings.text.weightValue" type="number" min="100" max="900" step="50" class="control w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        </field-label>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="文字の間隔" v-model="settings.text.tracking" :options="options.tracking"></select-field>
                            <select-field label="単語の折り返し" v-model="settings.text.wordBreak" :options="options.wordBreak"></select-field>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold">行間</span>
                                <select v-model="settings.text.leadingMode" class="control rounded-md border border-slate-300 bg-white px-2 py-1 text-xs">
                                    <option value="preset">プリセット</option>
                                    <option value="custom">数値指定</option>
                                </select>
                            </div>
                            <select v-if="settings.text.leadingMode === 'preset'" v-model="settings.text.leading" class="control w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                <option v-for="item in options.leading" :key="item[0]" :value="item[0]">{{ item[1] }}</option>
                            </select>
                            <div v-else class="grid grid-cols-[minmax(0,1fr)_100px] gap-2">
                                <input v-model.number="settings.text.leadingValue" type="number" min="0.1" max="500" step="0.05" aria-label="行間の数値" class="control min-w-0 rounded-md border border-slate-300 px-3 py-2 text-sm">
                                <select v-model="settings.text.leadingUnit" aria-label="行間の単位" class="control rounded-md border border-slate-300 bg-white px-2 py-2 text-sm">
                                    <option value="">単位なし</option>
                                    <option v-for="unit in options.sizeUnits" :key="unit" :value="unit">{{ unit }}</option>
                                </select>
                            </div>
                        </div>
                        <color-field label="文字色" v-model="settings.text.color"></color-field>
                        <div class="grid grid-cols-2 gap-3">
                            <toggle-field label="斜体" v-model="settings.text.italic"></toggle-field>
                            <toggle-field label="下線" v-model="settings.text.underline"></toggle-field>
                        </div>
                    </template>

                    <template v-if="genre === 'button'">
                        <field-label label="ボタンラベル"><input v-model="settings.button.content" class="control w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></field-label>
                        <select-field label="サイズ" v-model="settings.button.size" :options="options.buttonSize"></select-field>
                        <div class="grid grid-cols-2 gap-3">
                            <color-field label="背景色" v-model="settings.button.background"></color-field>
                            <color-field label="文字色" v-model="settings.button.color"></color-field>
                        </div>
                        <select-field label="角丸" v-model="settings.button.radius" :options="options.radius"></select-field>
                        <select-field label="影" v-model="settings.button.shadow" :options="options.shadow"></select-field>
                        <div class="grid grid-cols-2 gap-3">
                            <toggle-field label="枠線" v-model="settings.button.border"></toggle-field>
                            <toggle-field label="アイコン" v-model="settings.button.icon"></toggle-field>
                        </div>
                    </template>

                    <template v-if="genre === 'card'">
                        <field-label label="見出し"><input v-model="settings.card.title" class="control w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></field-label>
                        <field-label label="本文"><textarea v-model="settings.card.body" rows="3" class="control w-full resize-none rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea></field-label>
                        <div class="grid grid-cols-2 gap-3">
                            <color-field label="背景色" v-model="settings.card.background"></color-field>
                            <color-field label="アクセント" v-model="settings.card.accent"></color-field>
                        </div>
                        <select-field label="余白" v-model="settings.card.padding" :options="options.padding"></select-field>
                        <select-field label="角丸" v-model="settings.card.radius" :options="options.radius"></select-field>
                        <select-field label="影" v-model="settings.card.shadow" :options="options.shadow"></select-field>
                        <toggle-field label="上部アクセント" v-model="settings.card.line"></toggle-field>
                    </template>

                    <template v-if="genre === 'layout'">
                        <div>
                            <div class="mb-2 flex items-center justify-between"><label class="text-sm font-semibold">カラム数</label><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ settings.layout.columns }}</span></div>
                            <input v-model.number="settings.layout.columns" type="range" min="1" max="4" step="1" class="w-full">
                        </div>
                        <select-field label="間隔" v-model="settings.layout.gap" :options="options.gap"></select-field>
                        <select-field label="配置" v-model="settings.layout.align" :options="options.items"></select-field>
                        <color-field label="アイテム色" v-model="settings.layout.color"></color-field>
                        <toggle-field label="モバイルで1列" v-model="settings.layout.responsive"></toggle-field>
                    </template>
                </div>
            </aside>

            <section class="min-w-0 space-y-5">
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <div class="flex items-center gap-2 text-sm font-bold"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>ライブプレビュー</div>
                        <span class="font-mono text-xs text-slate-400">{{ viewportWidth }}</span>
                    </div>
                    <div class="checkerboard min-h-[430px] overflow-auto p-4 sm:p-8">
                        <div :style="previewFrameStyle" class="mx-auto flex min-h-[360px] items-center justify-center overflow-hidden bg-white p-5 shadow-sm transition-all duration-300 sm:p-10">
                            <div v-if="genre === 'text'" :class="generatedClasses" :style="generatedStyle">{{ settings.text.content }}</div>
                            <button v-if="genre === 'button'" type="button" :class="generatedClasses" :style="generatedStyle">
                                <i v-if="settings.button.icon" data-lucide="sparkles" class="h-4 w-4"></i>{{ settings.button.content }}
                            </button>
                            <article v-if="genre === 'card'" :class="generatedClasses" :style="generatedStyle">
                                <div v-if="settings.card.line" class="h-1.5 w-full" :style="{backgroundColor: settings.card.accent}"></div>
                                <div :class="settings.card.padding">
                                    <div class="mb-4 grid h-10 w-10 place-items-center rounded-md text-white" :style="{backgroundColor: settings.card.accent}"><i data-lucide="layers-3" class="h-5 w-5"></i></div>
                                    <h3 class="text-lg font-bold text-slate-900">{{ settings.card.title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ settings.card.body }}</p>
                                </div>
                            </article>
                            <div v-if="genre === 'layout'" :class="generatedClasses" class="w-full">
                                <div v-for="n in settings.layout.columns" :key="n" class="grid min-h-24 place-items-center rounded-md font-bold text-white" :style="{backgroundColor: settings.layout.color, opacity: 1 - ((n - 1) * .12)}">Item {{ n }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-800 bg-slate-950 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div class="flex items-center gap-2 text-sm font-bold text-slate-200"><i data-lucide="code-2" class="h-4 w-4 text-teal-400"></i>生成コード</div>
                        <button type="button" @click="copyCode" class="inline-flex h-8 items-center gap-2 rounded border border-slate-700 px-3 text-xs font-bold text-slate-300 transition hover:bg-slate-800" :title="copied ? 'コピーしました' : 'コードをコピー'">
                            <i :data-lucide="copied ? 'check' : 'copy'" class="h-3.5 w-3.5"></i>{{ copied ? 'コピー済み' : 'コピー' }}
                        </button>
                    </div>
                    <pre class="max-h-72 overflow-auto p-5 text-sm leading-6 text-slate-300"><code>{{ generatedCode }}</code></pre>
                </div>
            </section>
        </div>
    </main>
    <div v-if="toast" class="fixed bottom-5 left-1/2 -translate-x-1/2 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-lg">{{ toast }}</div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp, nextTick } = Vue;

const defaults = {
    text: { content: 'デザインを、もっと自由に。', sizeMode: 'preset', size: 'text-4xl', sizeValue: 36, sizeUnit: 'px', family: 'font-sans', weightMode: 'preset', weight: 'font-bold', weightValue: 700, align: 'text-center', leadingMode: 'preset', leading: 'leading-tight', leadingValue: 1.25, leadingUnit: '', tracking: 'tracking-normal', wordBreak: 'break-normal', color: '#0f172a', italic: false, underline: false },
    button: { content: '詳しく見る', size: 'px-5 py-3 text-sm', background: '#0f766e', color: '#ffffff', radius: 'rounded-md', shadow: 'shadow-md', border: false, icon: true },
    card: { title: 'プロジェクトを始めましょう', body: '設定したスタイルをリアルタイムで確認し、そのまま使えるTailwind CSSコードを生成できます。', background: '#ffffff', accent: '#0f766e', padding: 'p-6', radius: 'rounded-lg', shadow: 'shadow-lg', line: true },
    layout: { columns: 3, gap: 'gap-4', align: 'items-stretch', color: '#0f766e', responsive: true }
};

const options = {
    textSize: [['text-sm','小'],['text-base','標準'],['text-xl','大'],['text-4xl','特大'],['text-6xl','見出し']],
    sizeUnits: ['px','pt','rem','em'],
    valueMode: [['preset','プリセット'],['custom','数値指定']],
    fontFamily: [['font-sans','ゴシック（Sans Serif）'],['font-serif','明朝（Serif）'],['font-mono','等幅（Monospace）']],
    weight: [['font-normal','標準'],['font-medium','ミディアム'],['font-semibold','セミボールド'],['font-bold','ボールド'],['font-black','ブラック']],
    align: [['text-left','左揃え'],['text-center','中央揃え'],['text-right','右揃え']],
    leading: [['leading-none','詰める'],['leading-tight','狭い'],['leading-normal','標準'],['leading-relaxed','広い']],
    tracking: [['tracking-tighter','かなり狭い'],['tracking-tight','狭い'],['tracking-normal','標準'],['tracking-wide','広い'],['tracking-wider','かなり広い'],['tracking-widest','最大']],
    wordBreak: [['break-normal','通常'],['break-keep','単語を分割しない'],['break-words','必要な場合のみ'],['break-all','文字単位']],
    buttonSize: [['px-3 py-2 text-xs','小'],['px-5 py-3 text-sm','標準'],['px-7 py-4 text-base','大']],
    radius: [['rounded-none','なし'],['rounded','小'],['rounded-md','標準'],['rounded-lg','大'],['rounded-full','最大']],
    shadow: [['shadow-none','なし'],['shadow-sm','小'],['shadow-md','標準'],['shadow-lg','大']],
    padding: [['p-4','小'],['p-6','標準'],['p-8','大'],['p-10','特大']],
    gap: [['gap-2','狭い'],['gap-4','標準'],['gap-6','広い'],['gap-8','とても広い']],
    items: [['items-start','上'],['items-center','中央'],['items-end','下'],['items-stretch','伸ばす']]
};

const app = createApp({
    data: () => ({
        genre: 'text',
        settings: JSON.parse(JSON.stringify(defaults)),
        options,
        viewport: 'desktop',
        copied: false,
        toast: '',
        viewports: [
            { id: 'mobile', label: 'モバイル', icon: 'smartphone' },
            { id: 'tablet', label: 'タブレット', icon: 'tablet' },
            { id: 'desktop', label: 'デスクトップ', icon: 'monitor' }
        ]
    }),
    computed: {
        genreLabel() { return {text:'Typography',button:'Component',card:'Component',layout:'Layout'}[this.genre]; },
        genreTitle() { return {text:'テキストを装飾',button:'ボタンをデザイン',card:'カードをデザイン',layout:'グリッドを構成'}[this.genre]; },
        viewportWidth() { return {mobile:'375px',tablet:'768px',desktop:'100%'}[this.viewport]; },
        previewFrameStyle() { return { width: this.viewportWidth, maxWidth: '100%' }; },
        generatedClasses() {
            const s = this.settings[this.genre];
            if (this.genre === 'text') {
                const size = s.sizeMode === 'custom' ? this.numericClass('text', s.sizeValue, s.sizeUnit, 0.1, 500) : s.size;
                const weight = s.weightMode === 'custom' ? this.numericClass('font', s.weightValue, '', 100, 900) : s.weight;
                const leading = s.leadingMode === 'custom' ? this.numericClass('leading', s.leadingValue, s.leadingUnit, 0.1, 500) : s.leading;
                return [size,s.family,weight,s.align,leading,s.tracking,s.wordBreak,s.italic?'italic':'',s.underline?'underline':'','whitespace-pre-line'].filter(Boolean).join(' ');
            }
            if (this.genre === 'button') return [s.size,s.radius,s.shadow,'inline-flex items-center justify-center gap-2 font-semibold transition hover:brightness-110',s.border?'border border-current':''].filter(Boolean).join(' ');
            if (this.genre === 'card') return ['w-full max-w-sm overflow-hidden border border-slate-200',s.radius,s.shadow].filter(Boolean).join(' ');
            return ['grid',s.responsive?'grid-cols-1 sm:grid-cols-'+s.columns:'grid-cols-'+s.columns,s.gap,s.align].join(' ');
        },
        generatedStyle() {
            const s = this.settings[this.genre];
            if (this.genre === 'text') return { color: s.color };
            if (this.genre === 'button') return { backgroundColor: s.background, color: s.color };
            if (this.genre === 'card') return { backgroundColor: s.background };
            return {};
        },
        generatedCode() {
            const esc = value => String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            const s = this.settings[this.genre];
            if (this.genre === 'text') return `<p class="${this.generatedClasses}" style="color: ${s.color}">\n  ${esc(s.content)}\n</p>`;
            if (this.genre === 'button') return `<button class="${this.generatedClasses}" style="background-color: ${s.background}; color: ${s.color}">\n${s.icon?'  <svg><!-- icon --></svg>\n':''}  ${esc(s.content)}\n</button>`;
            if (this.genre === 'card') return `<article class="${this.generatedClasses}" style="background-color: ${s.background}">\n${s.line?`  <div class="h-1.5 w-full" style="background-color: ${s.accent}"></div>\n`:''}  <div class="${s.padding}">\n    <h3 class="text-lg font-bold text-slate-900">${esc(s.title)}</h3>\n    <p class="mt-2 text-sm leading-6 text-slate-600">${esc(s.body)}</p>\n  </div>\n</article>`;
            const child = Array.from({length:s.columns},(_,i) => `  <div class="rounded-md p-6">Item ${i+1}</div>`).join('\n');
            return `<div class="${this.generatedClasses}">\n${child}\n</div>`;
        }
    },
    methods: {
        numericClass(prefix, value, unit, min, max) {
            const number = Number(value);
            const safeValue = Number.isFinite(number) ? Math.min(max, Math.max(min, number)) : min;
            return `${prefix}-[${safeValue}${unit}]`;
        },
        selectGenre(value) { this.genre = value; this.refreshIcons(); },
        resetCurrent() { this.settings[this.genre] = JSON.parse(JSON.stringify(defaults[this.genre])); this.showToast('設定をリセットしました'); this.refreshIcons(); },
        async copyCode() {
            try {
                await navigator.clipboard.writeText(this.generatedCode);
                this.copied = true;
                this.showToast('コードをコピーしました');
                setTimeout(() => { this.copied = false; this.refreshIcons(); }, 1800);
            } catch (error) { this.showToast('コピーできませんでした'); }
            this.refreshIcons();
        },
        showToast(message) { this.toast = message; setTimeout(() => this.toast = '', 1800); },
        refreshIcons() { nextTick(() => lucide.createIcons()); }
    },
    mounted() { this.refreshIcons(); },
    updated() { this.refreshIcons(); }
});

app.component('field-label', {
    props: ['label'],
    template: `<label class="block"><span class="mb-2 block text-sm font-semibold">{{ label }}</span><slot></slot></label>`
});
app.component('select-field', {
    props: ['label','modelValue','options'],
    emits: ['update:modelValue'],
    template: `<label class="block"><span class="mb-2 block text-sm font-semibold">{{ label }}</span><select :value="modelValue" @change="$emit('update:modelValue',$event.target.value)" class="control w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm"><option v-for="item in options" :key="item[0]" :value="item[0]">{{ item[1] }}</option></select></label>`
});
app.component('color-field', {
    props: ['label','modelValue'],
    emits: ['update:modelValue'],
    template: `<label class="block"><span class="mb-2 block text-sm font-semibold">{{ label }}</span><span class="flex h-10 items-center gap-2 rounded-md border border-slate-300 bg-white px-2"><input type="color" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)" class="h-6 w-7 cursor-pointer"><span class="font-mono text-xs text-slate-600">{{ modelValue }}</span></span></label>`
});
app.component('toggle-field', {
    props: ['label','modelValue'],
    emits: ['update:modelValue'],
    template: `<label class="flex h-10 cursor-pointer items-center justify-between gap-2 rounded-md border border-slate-200 px-3"><span class="text-sm font-semibold">{{ label }}</span><input type="checkbox" :checked="modelValue" @change="$emit('update:modelValue',$event.target.checked)" class="h-4 w-4 accent-teal-700"></label>`
});
app.mount('#app');
</script>
</body>
</html>
