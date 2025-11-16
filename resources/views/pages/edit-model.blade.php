@extends('layouts.app')

@section('title', 'تعديل نموذج العمل التجاري')

@section('page-title', 'تعديل البيانات')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/data-input.css') }}">
@endpush

@section('content')
  <!-- Header -->
  <header style="text-align: center; margin-bottom: 30px;">
    <h1 style="font-size: 2rem; color: #2c3e50;">تعديل نموذج عملك التجاري</h1>
    <p style="color: #666;">الإصدار الحالي: {{ $businessModel->version }} | سيتم إنشاء الإصدار: {{ $businessModel->version + 1 }}</p>
  </header>

  <!-- Main Form Container -->
  <main>
    <form action="{{ route('business-model.update', $businessModel->id) }}" method="POST" id="updateForm">
      @csrf
      @method('PUT')

      <!-- 1. القيمة المضافة -->
      <section>
        <h2>القيمة المضافة (عرض القيمة)</h2>
        <p>ما الفائدة أو الميزة التي تقدمها لعملائك؟ مثل جودة أعلى أو سعر أقل أو توصيل سريع...</p>
        <textarea name="value_proposition" rows="4" placeholder="اكتب هنا..." required class="track-changes">{{ old('value_proposition', $businessModel->valuePropositions->first()->content ?? '') }}</textarea>
        <div>
          <p>اقتراحات:</p>
          <button type="button" onclick="addSuggestion('value_proposition', 'توصيل سريع')">توصيل سريع</button>
          <button type="button" onclick="addSuggestion('value_proposition', 'دعم فني دائم')">دعم فني دائم</button>
          <button type="button" onclick="addSuggestion('value_proposition', 'منتجات محلية الصنع')">منتجات محلية الصنع</button>
          <button type="button" onclick="addSuggestion('value_proposition', 'أسعار منافسة')">أسعار منافسة</button>
        </div>
      </section>

      <!-- 2. الشريحة المستهدفة -->
      <section>
        <h2>الشريحة المستهدفة</h2>
        <p>من هم عملاؤك؟ حدد أعمارهم، منطقتهم، واهتماماتهم.</p>
        <label class="optional">الفئة العمرية:</label>
        <select name="age_group" class="track-changes">
            <option value="">غير محدد</option>
            <option {{ old('age_group', $businessModel->customerSegments->first()->age_group ?? '') == 'أطفال' ? 'selected' : '' }}>أطفال</option>
            <option {{ old('age_group', $businessModel->customerSegments->first()->age_group ?? '') == 'شباب (في العشرينات من العمر)' ? 'selected' : '' }}>شباب (في العشرينات من العمر)</option>
            <option {{ old('age_group', $businessModel->customerSegments->first()->age_group ?? '') == 'كهول (مابين الثلاثين والخمسين من العمر)' ? 'selected' : '' }}>كهول (مابين الثلاثين والخمسين من العمر)</option>
            <option {{ old('age_group', $businessModel->customerSegments->first()->age_group ?? '') == 'كبار السن (فوق الخمسين)' ? 'selected' : '' }}>كبار السن (فوق الخمسين)</option>
            <option {{ old('age_group', $businessModel->customerSegments->first()->age_group ?? '') == 'جميع الأعمار' ? 'selected' : '' }}>جميع الأعمار</option>
        </select>

        <label>المنطقة الجغرافية: <span style="color: red;">*</span></label>
        <select name="region" required class="track-changes">
          <option value="">اختر المنطقة</option>
          <option value="محلي" {{ old('region', $businessModel->customerSegments->first()->region ?? '') == 'محلي' ? 'selected' : '' }}>محلي</option>
          <option value="وطني" {{ old('region', $businessModel->customerSegments->first()->region ?? '') == 'وطني' ? 'selected' : '' }}>وطني</option>
          <option value="دولي" {{ old('region', $businessModel->customerSegments->first()->region ?? '') == 'دولي' ? 'selected' : '' }}>دولي</option>
        </select>

        <textarea name="target_notes" rows="3" placeholder="تفاصيل إضافية..." class="track-changes">{{ old('target_notes', $businessModel->customerSegments->first()->notes ?? '') }}</textarea>
      </section>

      <!-- 3. العلاقات مع العملاء -->
      @php
        $currentRelationship = $businessModel->customerRelationships->first();
        $relationshipValue = old('relationship', $currentRelationship ? ($currentRelationship->relationshipType ? $currentRelationship->relationshipType->code : 'other') : '');
      @endphp
      <section>
        <h2>العلاقات مع العملاء <span style="color: red;">*</span></h2>
        <p>كيف تتعامل مع عملائك؟ هل هي خدمة ذاتية أم مساعدة شخصية؟</p>
        <label><input type="radio" name="relationship" value="self_service" {{ $relationshipValue == 'self_service' ? 'checked' : '' }} required class="track-changes"> خدمة ذاتية</label>
        <label><input type="radio" name="relationship" value="personal_assist" {{ $relationshipValue == 'personal_assist' ? 'checked' : '' }} class="track-changes"> مساعدة شخصية</label>
        <label><input type="radio" name="relationship" value="automated" {{ $relationshipValue == 'automated' ? 'checked' : '' }} class="track-changes"> خدمة آلية</label>
        <label><input type="radio" name="relationship" value="co_creation" {{ $relationshipValue == 'co_creation' ? 'checked' : '' }} class="track-changes"> شراكة مع العميل</label>
        <label><input type="radio" name="relationship" value="other" {{ $relationshipValue == 'other' ? 'checked' : '' }} class="track-changes"> أخرى:</label>
        <input type="text" name="relationship_other" placeholder="اكتب هنا..." value="{{ old('relationship_other', $currentRelationship && !$currentRelationship->relationshipType ? $currentRelationship->details : '') }}" class="track-changes">
      </section>

      <!-- 4. القنوات -->
      @php
        $selectedChannels = old('channels', $businessModel->channels->pluck('channelType.code')->filter()->toArray());
        $hasOtherChannel = $businessModel->channels->contains(fn($ch) => !$ch->channelType);
      @endphp
      <section>
        <h2>القنوات <span style="color: red;">*</span></h2>
        <p>كيف تصل إلى عملائك أو كيف يصلون إليك؟</p>
        <label><input type="checkbox" name="channels[]" value="online_store" {{ in_array('online_store', $selectedChannels) ? 'checked' : '' }} class="track-changes"> متجر إلكتروني</label>
        <label><input type="checkbox" name="channels[]" value="mobile_app" {{ in_array('mobile_app', $selectedChannels) ? 'checked' : '' }} class="track-changes"> تطبيق جوال</label>
        <label><input type="checkbox" name="channels[]" value="physical_store" {{ in_array('physical_store', $selectedChannels) ? 'checked' : '' }} class="track-changes"> متجر فعلي</label>
        <label><input type="checkbox" name="channels[]" value="agents" {{ in_array('agents', $selectedChannels) ? 'checked' : '' }} class="track-changes"> مندوبين أو وكلاء</label>
        <label><input type="checkbox" name="channels[]" value="other" {{ $hasOtherChannel || in_array('other', $selectedChannels) ? 'checked' : '' }} class="track-changes"> أخرى:</label>
        <input type="text" name="channel_other" placeholder="اكتب هنا..." value="{{ old('channel_other', $businessModel->channels->where('channelType', null)->first()->details ?? '') }}" class="track-changes">
      </section>

      <!-- 5. الأنشطة الرئيسية -->
      <section>
        <h2>الأنشطة الرئيسية <span style="color: red;">*</span></h2>
        <p>ما الأنشطة الأساسية التي يعتمد عليها عملك؟</p>
        <textarea name="key_activities" rows="4" placeholder="اكتب هنا..." required class="track-changes">{{ old('key_activities', $businessModel->keyActivities->first()->description ?? '') }}</textarea>
        <div>
          <p>اقتراحات:</p>
          <button type="button" onclick="addSuggestion('key_activities', 'التصنيع')">التصنيع</button>
          <button type="button" onclick="addSuggestion('key_activities', 'التسويق')">التسويق</button>
          <button type="button" onclick="addSuggestion('key_activities', 'البيع')">البيع</button>
          <button type="button" onclick="addSuggestion('key_activities', 'التطوير التقني')">التطوير التقني</button>
        </div>
      </section>

      <!-- 6. الموارد الرئيسية -->
      @php
        $selectedResources = old('resources', $businessModel->keyResources->pluck('resourceType.code')->filter()->toArray());
        $resourceDetails = [
            'human' => old('human_details', $businessModel->keyResources->where('resourceType.code', 'human')->first()->details ?? ''),
            'technical' => old('technical_details', $businessModel->keyResources->where('resourceType.code', 'technical')->first()->details ?? ''),
            'equipment' => old('equipment_details', $businessModel->keyResources->where('resourceType.code', 'equipment')->first()->details ?? ''),
            'other' => old('resource_other_details', $businessModel->keyResources->where('resourceType.code', 'other')->first()->details ?? '')
        ];
      @endphp
      <section>
        <h2>الموارد الرئيسية <span style="color: red;">*</span></h2>
        <p>ما الموارد التي تحتاجها لتشغيل عملك؟</p>

        <label><input type="checkbox" name="resources[]" value="human" {{ in_array('human', $selectedResources) ? 'checked' : '' }} class="track-changes"> بشرية</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد البشرية:</label>
          <textarea name="human_details" placeholder="مثال: محاسب، خبير تصنيع، 10 عمال..." class="track-changes">{{ $resourceDetails['human'] }}</textarea>
        </div>

        <label><input type="checkbox" name="resources[]" value="technical" {{ in_array('technical', $selectedResources) ? 'checked' : '' }} class="track-changes"> تقنية</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد التقنية:</label>
          <textarea name="technical_details" placeholder="مثال: حاسوب ، هاتف خدمة العملاء، برمجيات..." class="track-changes">{{ $resourceDetails['technical'] }}</textarea>
        </div>

        <label><input type="checkbox" name="resources[]" value="equipment" {{ in_array('equipment', $selectedResources) ? 'checked' : '' }} class="track-changes"> معدات</label>
        <div class="resource-detail">
          <label>تفاصيل المعدات:</label>
          <textarea name="equipment_details" placeholder="مثال: آلات، أدوات، مركبات..." class="track-changes">{{ $resourceDetails['equipment'] }}</textarea>
        </div>

        <label><input type="checkbox" name="resources[]" value="other" {{ in_array('other', $selectedResources) ? 'checked' : '' }} class="track-changes"> أخرى</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد الأخرى:</label>
          <textarea name="resource_other_details" placeholder="اكتب هنا..." class="track-changes">{{ $resourceDetails['other'] }}</textarea>
        </div>
      </section>

      <!-- 7. الشراكات الرئيسية -->
      <section>
        <h2>الشراكات الرئيسية</h2>
        <p>من هي الشركات والأفراد الذين تحتاج شركتك للعمل معهم لإنجاز أنشطتها؟</p>
        <textarea name="key_partners" rows="3" placeholder="اكتب هنا..." class="track-changes">{{ old('key_partners', $businessModel->keyPartnerships->first()->description ?? '') }}</textarea>
      </section>

      <!-- 8. مصادر الدخل -->
      @php
        $selectedIncome = old('income', $businessModel->revenueStreams->pluck('streamType.code')->filter()->toArray());
        $hasOtherIncome = $businessModel->revenueStreams->contains(fn($rs) => !$rs->streamType);
      @endphp
      <section>
        <h2>مصادر الدخل <span style="color: red;">*</span></h2>
        <p>كيف تكسب المال من عملك؟</p>
        <label><input type="checkbox" name="income[]" value="sales" {{ in_array('sales', $selectedIncome) ? 'checked' : '' }} class="track-changes"> مبيعات مباشرة</label>
        <label><input type="checkbox" name="income[]" value="subscriptions" {{ in_array('subscriptions', $selectedIncome) ? 'checked' : '' }} class="track-changes"> اشتراكات</label>
        <label><input type="checkbox" name="income[]" value="ads" {{ in_array('ads', $selectedIncome) ? 'checked' : '' }} class="track-changes"> إعلانات</label>
        <label><input type="checkbox" name="income[]" value="commissions" {{ in_array('commissions', $selectedIncome) ? 'checked' : '' }} class="track-changes"> عمولات</label>
        <label><input type="checkbox" name="income[]" value="other" {{ $hasOtherIncome || in_array('other', $selectedIncome) ? 'checked' : '' }} class="track-changes"> أخرى:</label>
        <input type="text" name="income_other" placeholder="اكتب هنا..." value="{{ old('income_other', $businessModel->revenueStreams->where('streamType', null)->first()->details ?? '') }}" class="track-changes">
      </section>

      <!-- 9. هيكل التكلفة -->
      <section>
        <h2>هيكل التكلفة</h2>
        <p>ما هي التكاليف الأساسية لتشغيل المشروع؟</p>

        <label>العملة: <span style="color: red;">*</span></label>
        <select id="currencySelect" name="currency" required class="track-changes">
          <option value="$" {{ old('currency', $businessModel->currency_code) == '$' ? 'selected' : '' }}>دولار أمريكي ($)</option>
          <option value="₺" {{ old('currency', $businessModel->currency_code) == '₺' ? 'selected' : '' }}>ليرة تركية (₺)</option>
          <option value="ل.س" {{ old('currency', $businessModel->currency_code) == 'ل.س' ? 'selected' : '' }}>ليرة سورية (ل.س)</option>
        </select>

        <div class="cost-table-container">
          <table class="cost-table">
            <thead>
              <tr>
                <th>نوع المصروف</th>
                <th>الوصف</th>
                <th>التكلفة (<span class="currency-symbol">{{ $businessModel->currency_code }}</span>)</th>
                <th>الكمية</th>
                <th>المجموع (<span class="currency-symbol">{{ $businessModel->currency_code }}</span>)</th>
              </tr>
            </thead>
            <tbody id="costTableBody">
              @php $rowCounter = 0; @endphp
              @foreach($businessModel->expenses->sortBy('display_order') as $expense)
                @php $rowCounter++; @endphp
                <tr>
                  <td><input type="text" placeholder="مثال: مواد خام" name="expense_type_{{ $rowCounter }}" value="{{ old("expense_type_$rowCounter", $expense->expense_type) }}" class="track-changes"></td>
                  <td><input type="text" placeholder="..." name="expense_desc_{{ $rowCounter }}" value="{{ old("expense_desc_$rowCounter", $expense->description) }}" class="track-changes"></td>
                  <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_{{ $rowCounter }}" class="cost-input track-changes" data-row="{{ $rowCounter }}" value="{{ old("expense_cost_$rowCounter", $expense->unit_cost) }}"></td>
                  <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_{{ $rowCounter }}" class="qty-input track-changes" data-row="{{ $rowCounter }}" value="{{ old("expense_qty_$rowCounter", $expense->quantity) }}"></td>
                  <td class="total-cell"><span class="row-total" data-row="{{ $rowCounter }}">۰</span></td>
                </tr>
              @endforeach

              @if($businessModel->expenses->count() == 0)
                <tr>
                  <td><input type="text" placeholder="مثال: مواد خام" name="expense_type_1" value="{{ old('expense_type_1') }}" class="track-changes"></td>
                  <td><input type="text" placeholder="..." name="expense_desc_1" value="{{ old('expense_desc_1') }}" class="track-changes"></td>
                  <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_1" class="cost-input track-changes" data-row="1" value="{{ old('expense_cost_1') }}"></td>
                  <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_1" class="qty-input track-changes" data-row="1" value="{{ old('expense_qty_1') }}"></td>
                  <td class="total-cell"><span class="row-total" data-row="1">۰</span></td>
                </tr>
                @php $rowCounter = 1; @endphp
              @endif
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" style="text-align: left;">المجموع الكلي</td>
                <td id="grandTotal">۰</td>
              </tr>
            </tfoot>
          </table>
        </div>
        <button type="button" class="add-row-btn" onclick="addCostRow()">إضافة صف جديد</button>
      </section>

      <!-- Buttons -->
      <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
        <button type="submit" id="updateBtn" disabled style="opacity: 0.5; cursor: not-allowed;">تحديث النموذج</button>
        <button type="button" onclick="window.location.href='{{ route('business-model.show', $businessModel->id) }}'">إلغاء</button>
      </div>

    </form>
  </main>
@endsection

@push('scripts')
  <script>
    let rowCounter = {{ $rowCounter }};
    let hasChanges = false;
    let initialFormData = null;

    // Track changes
    function enableUpdateButton() {
      const updateBtn = document.getElementById('updateBtn');
      updateBtn.disabled = false;
      updateBtn.style.opacity = '1';
      updateBtn.style.cursor = 'pointer';
      hasChanges = true;
    }

    function captureInitialState() {
      const form = document.getElementById('updateForm');
      initialFormData = new FormData(form);
    }

    function checkForChanges() {
      const form = document.getElementById('updateForm');
      const currentFormData = new FormData(form);

      let changed = false;
      for (let [key, value] of currentFormData.entries()) {
        if (initialFormData.get(key) !== value) {
          changed = true;
          break;
        }
      }

      if (changed) {
        enableUpdateButton();
      }
    }

    // Add suggestion to textarea
    function addSuggestion(fieldName, text) {
      const textarea = document.querySelector(`[name="${fieldName}"]`);
      const currentValue = textarea.value.trim();
      if (currentValue) {
        textarea.value = currentValue + '، ' + text;
      } else {
        textarea.value = text;
      }
      enableUpdateButton();
    }

    // Convert Western numerals to Arabic-Indic numerals
    function toArabicNumerals(str) {
      const arabicNumerals = ['۰', '۱', '۲', '۳', '٤', '٥', '٦', '٧', '۸', '۹'];
      return str.toString().replace(/[0-9]/g, function(w) {
        return arabicNumerals[+w];
      });
    }

    // Convert Arabic-Indic numerals to Western numerals for calculations
    function toWesternNumerals(str) {
      const arabicToWestern = {
        '۰': '0', '۱': '1', '۲': '2', '۳': '3', '٤': '4',
        '٥': '5', '٦': '6', '٧': '7', '۸': '8', '۹': '9'
      };
      return str.replace(/[۰۱۲۳٤٥٦٧۸۹]/g, function(w) {
        return arabicToWestern[w];
      });
    }

    // Format number input to Arabic numerals
    function formatArabicNumber(input) {
      let value = input.value;
      let westernValue = toWesternNumerals(value);
      westernValue = westernValue.replace(/[^\d.]/g, '');
      const parts = westernValue.split('.');
      if (parts.length > 2) {
        westernValue = parts[0] + '.' + parts.slice(1).join('');
      }
      input.value = toArabicNumerals(westernValue);
    }

    // Update currency symbol
    function updateCurrencySymbol() {
      const select = document.getElementById('currencySelect');
      const symbol = select.value;
      document.querySelectorAll('.currency-symbol').forEach(el => {
        el.textContent = symbol;
      });
    }

    // Calculate row total and grand total
    function calculateTotals() {
      let grandTotal = 0;

      document.querySelectorAll('.cost-input').forEach(input => {
        const row = input.getAttribute('data-row');
        const costInput = document.querySelector(`.cost-input[data-row="${row}"]`);
        const qtyInput = document.querySelector(`.qty-input[data-row="${row}"]`);
        const totalSpan = document.querySelector(`.row-total[data-row="${row}"]`);

        const costValue = toWesternNumerals(costInput.value);
        const qtyValue = toWesternNumerals(qtyInput.value);

        const cost = parseFloat(costValue) || 0;
        const qty = parseFloat(qtyValue) || 0;
        const rowTotal = cost * qty;

        totalSpan.textContent = toArabicNumerals(rowTotal.toFixed(2));
        grandTotal += rowTotal;
      });

      document.getElementById('grandTotal').textContent = toArabicNumerals(grandTotal.toFixed(2));
    }

    // Add new row to cost table
    function addCostRow() {
      rowCounter++;
      const tbody = document.getElementById('costTableBody');
      const newRow = document.createElement('tr');

      newRow.innerHTML = `
        <td><input type="text" placeholder="نوع المصروف" name="expense_type_${rowCounter}" class="track-changes"></td>
        <td><input type="text" placeholder="..." name="expense_desc_${rowCounter}" class="track-changes"></td>
        <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_${rowCounter}" class="cost-input track-changes" data-row="${rowCounter}"></td>
        <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_${rowCounter}" class="qty-input track-changes" data-row="${rowCounter}"></td>
        <td class="total-cell"><span class="row-total" data-row="${rowCounter}">۰</span></td>
      `;

      tbody.appendChild(newRow);
      attachCalculationListeners(newRow);
      attachChangeListeners(newRow);
      enableUpdateButton();
    }

    // Attach event listeners to cost and quantity inputs
    function attachCalculationListeners(container) {
      container.querySelectorAll('.cost-input, .qty-input').forEach(input => {
        input.addEventListener('input', function() {
          formatArabicNumber(this);
          calculateTotals();
        });
      });
    }

    // Attach change tracking listeners
    function attachChangeListeners(container) {
      container.querySelectorAll('.track-changes').forEach(element => {
        element.addEventListener('change', enableUpdateButton);
        element.addEventListener('input', enableUpdateButton);
      });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      attachCalculationListeners(document);
      attachChangeListeners(document);

      document.getElementById('currencySelect').addEventListener('change', updateCurrencySymbol);

      updateCurrencySymbol();
      calculateTotals();

      // Capture initial state after a short delay
      setTimeout(captureInitialState, 100);
    });
  </script>
@endpush
