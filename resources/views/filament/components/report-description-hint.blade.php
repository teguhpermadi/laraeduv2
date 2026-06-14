<div class="space-y-3 text-sm">
    <p class="font-medium text-primary-600">Placeholder yang tersedia:</p>
    <div class="grid grid-cols-2 gap-1">
        @if ($type === 'knowledge')
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{student_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama lengkap siswa</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{student_nickname}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama panggilan siswa</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{materials_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Daftar materi yang lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{materials_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Daftar materi yang tidak lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{count_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Jumlah materi lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{count_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Jumlah materi tidak lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{highest_score}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nilai tertinggi</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{highest_score_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama materi nilai tertinggi</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{lowest_score}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nilai terendah</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{lowest_score_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama materi nilai terendah</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{average_score}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Rata-rata nilai</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{passing_grade}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">KKM</span>
        @else
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{student_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama lengkap siswa</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{student_nickname}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama panggilan siswa</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{skills_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Daftar keterampilan yang lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{skills_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Daftar keterampilan tidak lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{count_skill_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Jumlah keterampilan lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{count_skill_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Jumlah keterampilan tidak lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{highest_skill}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nilai keterampilan tertinggi</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{highest_skill_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama keterampilan nilai tertinggi</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{lowest_skill}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nilai keterampilan terendah</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{lowest_skill_name}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Nama keterampilan nilai terendah</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{average_skill}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Rata-rata nilai keterampilan</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{passing_grade}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">KKM</span>
        @endif
    </div>

    <p class="font-medium text-primary-600 mt-2">Block bersyarat (conditional):</p>
    <div class="grid grid-cols-2 gap-1">
        @if ($type === 'knowledge')
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{if_passed}...{/if_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Hanya muncul jika ada materi lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{if_not_passed}...{/if_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Hanya muncul jika ada materi tidak lulus</span>
        @else
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{if_skill_passed}...{/if_skill_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Hanya muncul jika ada keterampilan lulus</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">{if_skill_not_passed}...{/if_skill_not_passed}</code>
            <span class="text-xs text-gray-600 dark:text-gray-400">Hanya muncul jika ada keterampilan tidak lulus</span>
        @endif
    </div>

    <p class="font-medium text-primary-600 mt-2">Contoh template:</p>
    <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded text-xs">
        @if ($type === 'knowledge')
            <code>
Alhamdulillah, ananda {student_name}{if_passed} telah menguasai materi:<br/>
{materials_passed} nilai tertinggi {highest_score} pada "{highest_score_name}"{/if_passed}{if_not_passed}<br/>
tetapi masih perlu peningkatan pada: {materials_not_passed} nilai terendah {lowest_score} pada "{lowest_score_name}"{/if_not_passed}
            </code>
        @else
            <code>
Alhamdulillah, ananda {student_name}{if_skill_passed} telah menguasai keterampilan:<br/>
{skills_passed}{/if_skill_passed}{if_skill_not_passed}<br/>
tetapi masih perlu peningkatan pada keterampilan: {skills_not_passed}{/if_skill_not_passed}
            </code>
        @endif
    </div>

    <p class="text-xs text-gray-400 italic">Biarkan kosong untuk menggunakan template bawaan sistem.</p>
</div>
