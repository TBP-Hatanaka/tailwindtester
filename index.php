<?php
$genres = [
    'text' => ['label' => 'テキスト', 'icon' => 'type'],
    'image' => ['label' => '画像変形', 'icon' => 'scan'],
    'imageFilter' => ['label' => '画像フィルター', 'icon' => 'sliders-horizontal'],
    'block' => ['label' => 'ブロック', 'icon' => 'square'],
    'table' => ['label' => 'テーブル', 'icon' => 'table-2'],
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

                    <template v-if="genre === 'image'">
                        <field-label label="画像URL">
                            <textarea v-model="settings.image.src" rows="3" class="control w-full resize-none break-all rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </field-label>
                        <field-label label="代替テキスト">
                            <input v-model="settings.image.alt" class="control w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        </field-label>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold">画像サイズ</span>
                                <select v-model="settings.image.sizeMode" class="control rounded-md border border-slate-300 bg-white px-2 py-1 text-xs">
                                    <option value="preset">プリセット</option>
                                    <option value="custom">数値指定</option>
                                </select>
                            </div>
                            <select v-if="settings.image.sizeMode === 'preset'" v-model="settings.image.width" class="control w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                <option v-for="item in options.imageWidth" :key="item[0]" :value="item[0]">{{ item[1] }}</option>
                            </select>
                            <div v-else class="grid grid-cols-[minmax(0,1fr)_100px] gap-2">
                                <input v-model.number="settings.image.widthValue" type="number" min="1" max="1600" step="1" aria-label="画像幅" class="control min-w-0 rounded-md border border-slate-300 px-3 py-2 text-sm">
                                <select v-model="settings.image.widthUnit" aria-label="画像幅の単位" class="control rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                                    <option value="px">px</option><option value="%">%</option><option value="rem">rem</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="縦横比" v-model="settings.image.aspect" :options="options.imageAspect"></select-field>
                            <select-field label="表示方法" v-model="settings.image.fit" :options="options.imageFit"></select-field>
                        </div>
                        <select-field label="影" v-model="settings.image.shadow" :options="options.imageShadow"></select-field>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="枠線の太さ" v-model="settings.image.borderWidth" :options="options.blockBorderWidth"></select-field>
                            <select-field label="枠線の種類" v-model="settings.image.borderStyle" :options="options.blockBorderStyle"></select-field>
                        </div>
                        <color-field v-if="settings.image.borderWidth" label="枠線の色" v-model="settings.image.borderColor"></color-field>
                        <div>
                            <div class="mb-2 flex items-center justify-between"><label class="text-sm font-semibold">透明度</label><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ settings.image.opacity }}%</span></div>
                            <input v-model.number="settings.image.opacity" type="range" min="0" max="100" step="1" class="w-full">
                        </div>
                        <select-field label="マスク" v-model="settings.image.mask" :options="options.imageMask"></select-field>
                    </template>

                    <template v-if="genre === 'imageFilter'">
                        <field-label label="画像URL">
                            <textarea v-model="settings.imageFilter.src" rows="3" class="control w-full resize-none break-all rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </field-label>
                        <field-label label="代替テキスト">
                            <input v-model="settings.imageFilter.alt" class="control w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        </field-label>
                        <select-field label="画像サイズ" v-model="settings.imageFilter.width" :options="options.imageWidth"></select-field>
                        <range-field label="明るさ" v-model="settings.imageFilter.brightness" :min="0" :max="200" :step="1" suffix="%"></range-field>
                        <range-field label="コントラスト" v-model="settings.imageFilter.contrast" :min="0" :max="200" :step="1" suffix="%"></range-field>
                        <range-field label="彩度" v-model="settings.imageFilter.saturation" :min="0" :max="200" :step="1" suffix="%"></range-field>
                        <range-field label="グレースケール" v-model="settings.imageFilter.grayscale" :min="0" :max="100" :step="1" suffix="%"></range-field>
                        <range-field label="セピア" v-model="settings.imageFilter.sepia" :min="0" :max="100" :step="1" suffix="%"></range-field>
                        <range-field label="色相回転" v-model="settings.imageFilter.hue" :min="0" :max="360" :step="1" suffix="°"></range-field>
                        <range-field label="ぼかし" v-model="settings.imageFilter.blur" :min="0" :max="20" :step="0.5" suffix="px"></range-field>
                        <range-field label="色反転" v-model="settings.imageFilter.invert" :min="0" :max="100" :step="1" suffix="%"></range-field>
                    </template>

                    <template v-if="genre === 'block'">
                        <field-label label="ブロック内のテキスト">
                            <textarea v-model="settings.block.content" rows="2" class="control w-full resize-none rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </field-label>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="横幅" v-model="settings.block.width" :options="options.blockWidth"></select-field>
                            <select-field label="高さ" v-model="settings.block.height" :options="options.blockHeight"></select-field>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <color-field label="背景色" v-model="settings.block.background"></color-field>
                            <color-field label="文字色" v-model="settings.block.color"></color-field>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="内側の余白" v-model="settings.block.padding" :options="options.blockPadding"></select-field>
                            <select-field label="角丸" v-model="settings.block.radius" :options="options.blockRadius"></select-field>
                        </div>
                        <select-field label="影" v-model="settings.block.shadow" :options="options.blockShadow"></select-field>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="枠線の太さ" v-model="settings.block.borderWidth" :options="options.blockBorderWidth"></select-field>
                            <select-field label="枠線の種類" v-model="settings.block.borderStyle" :options="options.blockBorderStyle"></select-field>
                        </div>
                        <color-field v-if="settings.block.borderWidth" label="枠線の色" v-model="settings.block.borderColor"></color-field>
                        <select-field label="内容の配置" v-model="settings.block.alignment" :options="options.blockAlignment"></select-field>
                        <div>
                            <div class="mb-2 flex items-center justify-between"><label class="text-sm font-semibold">透明度</label><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ settings.block.opacity }}%</span></div>
                            <input v-model.number="settings.block.opacity" type="range" min="0" max="100" step="1" class="w-full">
                        </div>
                    </template>

                    <template v-if="genre === 'table'">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="mb-2 flex items-center justify-between"><label class="text-sm font-semibold">行数</label><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ settings.table.rows }}</span></div>
                                <input v-model.number="settings.table.rows" type="range" min="1" max="10" step="1" class="w-full">
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between"><label class="text-sm font-semibold">列数</label><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ settings.table.columns }}</span></div>
                                <input v-model.number="settings.table.columns" type="range" min="1" max="8" step="1" class="w-full">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="横幅" v-model="settings.table.width" :options="options.tableWidth"></select-field>
                            <select-field label="セル余白" v-model="settings.table.padding" :options="options.tablePadding"></select-field>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="文字配置" v-model="settings.table.align" :options="options.tableAlignment"></select-field>
                            <select-field label="影" v-model="settings.table.shadow" :options="options.blockShadow"></select-field>
                        </div>
                        <toggle-field label="見出し行を表示" v-model="settings.table.header"></toggle-field>
                        <div class="grid grid-cols-2 gap-3">
                            <select-field label="枠線の太さ" v-model="settings.table.borderWidth" :options="options.tableBorderWidth"></select-field>
                            <select-field label="枠線の種類" v-model="settings.table.borderStyle" :options="options.blockBorderStyle"></select-field>
                        </div>
                        <color-field label="枠線の色" v-model="settings.table.borderColor"></color-field>
                        <div class="grid grid-cols-2 gap-3">
                            <color-field label="見出し背景" v-model="settings.table.headerBackground"></color-field>
                            <color-field label="見出し文字" v-model="settings.table.headerColor"></color-field>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <color-field label="セル背景" v-model="settings.table.cellBackground"></color-field>
                            <color-field label="セル文字" v-model="settings.table.cellColor"></color-field>
                        </div>
                        <toggle-field label="行を交互色にする" v-model="settings.table.striped"></toggle-field>
                        <color-field v-if="settings.table.striped" label="交互行の背景色" v-model="settings.table.stripeColor"></color-field>
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
                            <img v-if="genre === 'image'" :src="settings.image.src" :alt="settings.image.alt" :class="generatedClasses" :style="generatedStyle">
                            <img v-if="genre === 'imageFilter'" :src="settings.imageFilter.src" :alt="settings.imageFilter.alt" :class="generatedClasses" :style="generatedStyle">
                            <div v-if="genre === 'block'" :class="generatedClasses" :style="generatedStyle">{{ settings.block.content }}</div>
                            <div v-if="genre === 'table'" class="w-full overflow-x-auto p-1">
                                <table :class="tableClasses" class="mx-auto">
                                    <thead v-if="settings.table.header" :style="{backgroundColor: settings.table.headerBackground, color: settings.table.headerColor}">
                                        <tr><th v-for="column in settings.table.columns" :key="column" :class="tableCellClasses">見出し {{ column }}</th></tr>
                                    </thead>
                                    <tbody :style="{color: settings.table.cellColor}">
                                        <tr v-for="row in settings.table.rows" :key="row" :style="{backgroundColor: settings.table.striped && row % 2 === 0 ? settings.table.stripeColor : settings.table.cellBackground}">
                                            <td v-for="column in settings.table.columns" :key="column" :class="tableCellClasses">セル {{ row }}-{{ column }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
    image: { src: 'https://tbpdigital.jp/wp-content/uploads/2026/05/service_02.png', alt: 'サービスイメージ', sizeMode: 'preset', width: 'w-96', widthValue: 480, widthUnit: 'px', aspect: 'aspect-auto', fit: 'object-cover', shadow: 'shadow-xl', borderWidth: '', borderStyle: 'border-solid', borderColor: '#0f766e', opacity: 100, mask: 'none' },
    imageFilter: { src: 'https://tbpdigital.jp/wp-content/uploads/2026/05/service_02.png', alt: 'フィルター適用イメージ', width: 'w-96', brightness: 100, contrast: 100, saturation: 100, grayscale: 0, sepia: 0, hue: 0, blur: 0, invert: 0 },
    block: { content: 'ブロックコンテンツ', width: 'w-80', height: 'min-h-40', background: '#0f766e', color: '#ffffff', padding: 'p-6', radius: 'rounded-md', shadow: 'shadow-lg', borderWidth: '', borderStyle: 'border-solid', borderColor: '#134e4a', alignment: 'items-center justify-center text-center', opacity: 100 },
    table: { rows: 4, columns: 3, width: 'w-full', padding: 'px-4 py-3', align: 'text-left', shadow: 'shadow-md', header: true, borderWidth: 'border', borderStyle: 'border-solid', borderColor: '#cbd5e1', headerBackground: '#0f766e', headerColor: '#ffffff', cellBackground: '#ffffff', cellColor: '#334155', striped: true, stripeColor: '#f1f5f9' },
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
    imageWidth: [['w-48','小（192px）'],['w-64','中（256px）'],['w-96','大（384px）'],['w-full','横幅いっぱい']],
    imageAspect: [['aspect-auto','元画像'],['aspect-square','正方形'],['aspect-video','16:9'],['aspect-[4/3]','4:3']],
    imageFit: [['object-cover','範囲を覆う'],['object-contain','全体を表示'],['object-fill','範囲に合わせる']],
    imageShadow: [['shadow-none','なし'],['shadow-md','標準'],['shadow-xl','大'],['shadow-2xl','最大']],
    imageMask: [['none','なし'],['circle','円形'],['ellipse','楕円'],['diamond','ひし形'],['hexagon','六角形'],['slant','斜め'],['fade','下へフェード']],
    blockWidth: [['w-48','小'],['w-80','標準'],['w-96','大'],['w-full','横幅いっぱい']],
    blockHeight: [['min-h-24','低い'],['min-h-40','標準'],['min-h-64','高い'],['min-h-80','最大']],
    blockPadding: [['p-0','なし'],['p-3','小'],['p-6','標準'],['p-10','大']],
    blockRadius: [['rounded-none','なし'],['rounded','小'],['rounded-md','標準'],['rounded-lg','大'],['rounded-full','最大']],
    blockShadow: [['shadow-none','なし'],['shadow-sm','小'],['shadow-lg','標準'],['shadow-2xl','大']],
    blockBorderWidth: [['','なし'],['border','1px'],['border-2','2px'],['border-4','4px'],['border-8','8px']],
    blockBorderStyle: [['border-solid','実線'],['border-dashed','破線'],['border-dotted','点線'],['border-double','二重線']],
    blockAlignment: [['items-start justify-start text-left','左上'],['items-center justify-center text-center','中央'],['items-end justify-end text-right','右下']],
    tableWidth: [['w-auto','内容に合わせる'],['w-3/4','75%'],['w-full','100%']],
    tablePadding: [['px-2 py-1','狭い'],['px-4 py-3','標準'],['px-6 py-4','広い']],
    tableAlignment: [['text-left','左揃え'],['text-center','中央揃え'],['text-right','右揃え']],
    tableBorderWidth: [['border-0','なし'],['border','1px'],['border-2','2px'],['border-4','4px']],
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
        genreLabel() { return {text:'Typography',image:'Image transform',imageFilter:'Image filter',block:'Block style',table:'Table style',layout:'Layout'}[this.genre]; },
        genreTitle() { return {text:'テキストを装飾',image:'画像を変形',imageFilter:'画像にフィルターを適用',block:'ブロックを装飾',table:'テーブルを装飾',layout:'グリッドを構成'}[this.genre]; },
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
            if (this.genre === 'image') {
                const width = s.sizeMode === 'custom' ? this.numericClass('w', s.widthValue, s.widthUnit, 1, 1600) : s.width;
                const opacity = `opacity-[${Math.min(100, Math.max(0, Number(s.opacity) || 0)) / 100}]`;
                const borderColor = s.borderWidth ? `border-[${s.borderColor}]` : '';
                return [width,'max-w-full',s.aspect,s.fit,s.shadow,s.borderWidth,s.borderStyle,borderColor,opacity,this.imageMaskClass].filter(Boolean).join(' ');
            }
            if (this.genre === 'imageFilter') {
                return [s.width,'max-w-full',`brightness-[${s.brightness}%]`,`contrast-[${s.contrast}%]`,`saturate-[${s.saturation}%]`,`grayscale-[${s.grayscale}%]`,`sepia-[${s.sepia}%]`,`hue-rotate-[${s.hue}deg]`,`blur-[${s.blur}px]`,`invert-[${s.invert}%]`].join(' ');
            }
            if (this.genre === 'block') {
                const opacity = `opacity-[${Math.min(100, Math.max(0, Number(s.opacity) || 0)) / 100}]`;
                const background = `bg-[${s.background}]`;
                const color = `text-[${s.color}]`;
                const borderColor = s.borderWidth ? `border-[${s.borderColor}]` : '';
                return ['flex','max-w-full',s.width,s.height,s.padding,s.radius,s.shadow,s.borderWidth,s.borderStyle,borderColor,background,color,s.alignment,opacity,'whitespace-pre-line'].filter(Boolean).join(' ');
            }
            if (this.genre === 'table') return this.tableClasses;
            return ['grid',s.responsive?'grid-cols-1 sm:grid-cols-'+s.columns:'grid-cols-'+s.columns,s.gap,s.align].join(' ');
        },
        tableClasses() {
            const s = this.settings.table;
            return ['border-collapse','overflow-hidden',s.width,s.shadow].join(' ');
        },
        tableCellClasses() {
            const s = this.settings.table;
            return [s.padding,s.align,s.borderWidth,s.borderStyle,`border-[${s.borderColor}]`,'text-sm'].filter(Boolean).join(' ');
        },
        generatedStyle() {
            const s = this.settings[this.genre];
            if (this.genre === 'text') return { color: s.color };
            if (this.genre === 'image') return { ...this.imageMaskStyle, borderColor: s.borderColor };
            if (this.genre === 'imageFilter') return { filter: `brightness(${s.brightness}%) contrast(${s.contrast}%) saturate(${s.saturation}%) grayscale(${s.grayscale}%) sepia(${s.sepia}%) hue-rotate(${s.hue}deg) blur(${s.blur}px) invert(${s.invert}%)` };
            if (this.genre === 'block') return { backgroundColor: s.background, color: s.color, borderColor: s.borderColor };
            return {};
        },
        imageMaskClass() {
            const mask = this.settings.image.mask;
            return {
                none: '', circle: '[clip-path:circle(50%)]', ellipse: '[clip-path:ellipse(48%_38%)]',
                diamond: '[clip-path:polygon(50%_0%,100%_50%,50%_100%,0%_50%)]',
                hexagon: '[clip-path:polygon(25%_5%,75%_5%,100%_50%,75%_95%,25%_95%,0%_50%)]',
                slant: '[clip-path:polygon(12%_0%,100%_0%,88%_100%,0%_100%)]',
                fade: '[mask-image:linear-gradient(to_bottom,black_65%,transparent)]'
            }[mask] || '';
        },
        imageMaskStyle() {
            const mask = this.settings.image.mask;
            const clips = { circle:'circle(50%)', ellipse:'ellipse(48% 38%)', diamond:'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)', hexagon:'polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%)', slant:'polygon(12% 0%, 100% 0%, 88% 100%, 0% 100%)' };
            if (mask === 'fade') return { maskImage:'linear-gradient(to bottom, black 65%, transparent)', WebkitMaskImage:'linear-gradient(to bottom, black 65%, transparent)' };
            return clips[mask] ? { clipPath: clips[mask] } : {};
        },
        generatedCode() {
            const esc = value => String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            const s = this.settings[this.genre];
            if (this.genre === 'text') return `<p class="${this.generatedClasses}" style="color: ${s.color}">\n  ${esc(s.content)}\n</p>`;
            if (this.genre === 'image') return `<img\n  src="${esc(s.src)}"\n  alt="${esc(s.alt)}"\n  class="${this.generatedClasses}"\n>`;
            if (this.genre === 'imageFilter') return `<img\n  src="${esc(s.src)}"\n  alt="${esc(s.alt)}"\n  class="${this.generatedClasses}"\n>`;
            if (this.genre === 'block') return `<div class="${this.generatedClasses}">\n  ${esc(s.content)}\n</div>`;
            if (this.genre === 'table') {
                const header = s.header ? `\n  <thead class="bg-[${s.headerBackground}] text-[${s.headerColor}]">\n    <tr>\n${Array.from({length:s.columns},(_,i) => `      <th class="${this.tableCellClasses}">見出し ${i + 1}</th>`).join('\n')}\n    </tr>\n  </thead>` : '';
                const rows = Array.from({length:s.rows},(_,row) => `    <tr${s.striped ? ` class="even:bg-[${s.stripeColor}]"` : ''}>\n${Array.from({length:s.columns},(_,column) => `      <td class="${this.tableCellClasses}">セル ${row + 1}-${column + 1}</td>`).join('\n')}\n    </tr>`).join('\n');
                return `<table class="${this.tableClasses} bg-[${s.cellBackground}] text-[${s.cellColor}]">${header}\n  <tbody>\n${rows}\n  </tbody>\n</table>`;
            }
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
app.component('range-field', {
    props: ['label','modelValue','min','max','step','suffix'],
    emits: ['update:modelValue'],
    template: `<label class="block"><span class="mb-2 flex items-center justify-between gap-3"><span class="text-sm font-semibold">{{ label }}</span><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ modelValue }}{{ suffix }}</span></span><input type="range" :value="modelValue" :min="min" :max="max" :step="step" @input="$emit('update:modelValue',Number($event.target.value))" class="w-full"></label>`
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
