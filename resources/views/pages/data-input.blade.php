@extends('layouts.app')

@section('title', 'إدخال نموذج العمل التجاري')

@section('page-title', 'إدخال البيانات')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/data-input.css') }}">
@endpush

@section('content')
  <!-- Header -->
  <header style="text-align: center; margin-bottom: 30px;">
    <h1 style="font-size: 2rem; color: #2c3e50;">أدخل معلومات نموذج عملك التجاري</h1>
  </header>

  <!-- Display Errors -->
  @if($errors->any())
    <div style="background-color: #fee; border: 1px solid #fcc; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
      <ul style="margin: 0; padding-right: 20px; color: #c33;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Main Form Container -->
  <main>
    <form action="{{ route('generate') }}" method="POST">
      @csrf

      <!-- 1. القيمة المضافة -->
      <section>
        <h2>القيمة المضافة (عرض القيمة)</h2>
        <p>ما الفائدة أو الميزة التي تقدمها لعملائك؟ مثل جودة أعلى أو سعر أقل أو توصيل سريع...</p>
        <textarea name="value_proposition" rows="4" placeholder="اكتب هنا..." required>{{ old('value_proposition') }}</textarea>
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
        <select name="age_group">
            <option value="">غير محدد</option>
            <option {{ old('age_group') == 'أطفال' ? 'selected' : '' }}>أطفال</option>
            <option {{ old('age_group') == 'شباب (في العشرينات من العمر)' ? 'selected' : '' }}>شباب (في العشرينات من العمر)</option>
            <option {{ old('age_group') == 'كهول (مابين الثلاثين والخمسين من العمر)' ? 'selected' : '' }}>كهول (مابين الثلاثين والخمسين من العمر)</option>
            <option {{ old('age_group') == 'كبار السن (فوق الخمسين)' ? 'selected' : '' }}>كبار السن (فوق الخمسين)</option>
            <option {{ old('age_group') == 'جميع الأعمار' ? 'selected' : '' }}>جميع الأعمار</option>
        </select>

        <label>المنطقة الجغرافية: <span style="color: red;">*</span></label>
        <select name="region" required>
          <option value="">اختر المنطقة</option>
          <option value="محلي" {{ old('region') == 'محلي' ? 'selected' : '' }}>محلي</option>
          <option value="وطني" {{ old('region') == 'وطني' ? 'selected' : '' }}>وطني</option>
          <option value="دولي" {{ old('region') == 'دولي' ? 'selected' : '' }}>دولي</option>
        </select>

        <textarea name="target_notes" rows="3" placeholder="تفاصيل إضافية...">{{ old('target_notes') }}</textarea>
      </section>

      <!-- 3. العلاقات مع العملاء -->
        <section>
            <h2>العلاقات مع العملاء <span style="color: red;">*</span></h2>
            <p>كيف تتعامل مع عملائك؟ هل هي خدمة ذاتية أم مساعدة شخصية؟</p>
            <label><input type="radio" name="relationship" value="self_service" {{ old('relationship') == 'self_service' ? 'checked' : '' }} required> خدمة ذاتية</label>
            <label><input type="radio" name="relationship" value="personal_assist" {{ old('relationship') == 'personal_assist' ? 'checked' : '' }}> مساعدة شخصية</label>
            <label><input type="radio" name="relationship" value="automated" {{ old('relationship') == 'automated' ? 'checked' : '' }}> خدمة آلية</label>
            <label><input type="radio" name="relationship" value="co_creation" {{ old('relationship') == 'co_creation' ? 'checked' : '' }}> شراكة مع العميل</label>
            <label><input type="radio" name="relationship" value="other" {{ old('relationship') == 'other' ? 'checked' : '' }}> أخرى:</label>
            <input type="text" name="relationship_other" placeholder="اكتب هنا..." value="{{ old('relationship_other') }}">
        </section>

        <!-- 4. القنوات -->
        <section>
            <h2>القنوات <span style="color: red;">*</span></h2>
            <p>كيف تصل إلى عملائك أو كيف يصلون إليك؟</p>
            <label><input type="checkbox" name="channels[]" value="online_store" {{ in_array('online_store', old('channels', [])) ? 'checked' : '' }}> متجر إلكتروني</label>
            <label><input type="checkbox" name="channels[]" value="mobile_app" {{ in_array('mobile_app', old('channels', [])) ? 'checked' : '' }}> تطبيق جوال</label>
            <label><input type="checkbox" name="channels[]" value="physical_store" {{ in_array('physical_store', old('channels', [])) ? 'checked' : '' }}> متجر فعلي</label>
            <label><input type="checkbox" name="channels[]" value="agents" {{ in_array('agents', old('channels', [])) ? 'checked' : '' }}> مندوبين أو وكلاء</label>
            <label><input type="checkbox" name="channels[]" value="other" {{ in_array('other', old('channels', [])) ? 'checked' : '' }}> أخرى:</label>
            <input type="text" name="channel_other" placeholder="اكتب هنا..." value="{{ old('channel_other') }}">
        </section>

      <!-- 5. الأنشطة الرئيسية -->
      <section>
        <h2>الأنشطة الرئيسية <span style="color: red;">*</span></h2>
        <p>ما الأنشطة الأساسية التي يعتمد عليها عملك؟</p>
        <textarea name="key_activities" rows="4" placeholder="اكتب هنا..." required>{{ old('key_activities') }}</textarea>
        <div>
          <p>اقتراحات:</p>
          <button type="button" onclick="addSuggestion('key_activities', 'التصنيع')">التصنيع</button>
          <button type="button" onclick="addSuggestion('key_activities', 'التسويق')">التسويق</button>
          <button type="button" onclick="addSuggestion('key_activities', 'البيع')">البيع</button>
          <button type="button" onclick="addSuggestion('key_activities', 'التطوير التقني')">التطوير التقني</button>
        </div>
      </section>

      <!-- 6. الموارد الرئيسية -->
      <section>
        <h2>الموارد الرئيسية <span style="color: red;">*</span></h2>
        <p>ما الموارد التي تحتاجها لتشغيل عملك؟</p>

        <label><input type="checkbox" name="resources[]" value="human" {{ in_array('human', old('resources', [])) ? 'checked' : '' }}> بشرية</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد البشرية:</label>
          <textarea name="human_details" placeholder="مثال: محاسب، خبير تصنيع، 10 عمال...">{{ old('human_details') }}</textarea>
        </div>
        <label><input type="checkbox" name="resources[]" value="technical" {{ in_array('technical', old('resources', [])) ? 'checked' : '' }}> تقنية</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد التقنية:</label>
          <textarea name="technical_details" placeholder="مثال: حاسوب ، هاتف خدمة العملاء، برمجيات...">{{ old('technical_details') }}</textarea>
        </div>

        <label><input type="checkbox" name="resources[]" value="equipment" {{ in_array('equipment', old('resources', [])) ? 'checked' : '' }}> معدات</label>
        <div class="resource-detail">
          <label>تفاصيل المعدات:</label>
          <textarea name="equipment_details" placeholder="مثال: آلات، أدوات، مركبات...">{{ old('equipment_details') }}</textarea>
        </div>

        <label><input type="checkbox" name="resources[]" value="other" {{ in_array('other', old('resources', [])) ? 'checked' : '' }}> أخرى</label>
        <div class="resource-detail">
          <label>تفاصيل الموارد الأخرى:</label>
          <textarea name="resource_other_details" placeholder="اكتب هنا...">{{ old('resource_other_details') }}</textarea>
        </div>
      </section>

      <!-- 7. الشراكات الرئيسية -->
      <section>
        <h2>الشراكات الرئيسية</h2>
        <p>من هي الشركات والأفراد الذين تحتاج شركتك للعمل معهم لإنجاز أنشطتها؟</p>
        <textarea name="key_partners" rows="3" placeholder="اكتب هنا...">{{ old('key_partners') }}</textarea>
      </section>

      <!-- 8. مصادر الدخل -->
      <section>
        <h2>مصادر الدخل <span style="color: red;">*</span></h2>
        <p>كيف تكسب المال من عملك؟</p>
        <label><input type="checkbox" name="income[]" value="sales" {{ in_array('sales', old('income', [])) ? 'checked' : '' }}> مبيعات مباشرة</label>
        <label><input type="checkbox" name="income[]" value="subscriptions" {{ in_array('subscriptions', old('income', [])) ? 'checked' : '' }}> اشتراكات</label>
        <label><input type="checkbox" name="income[]" value="ads" {{ in_array('ads', old('income', [])) ? 'checked' : '' }}> إعلانات</label>
        <label><input type="checkbox" name="income[]" value="commissions" {{ in_array('commissions', old('income', [])) ? 'checked' : '' }}> عمولات</label>
        <label><input type="checkbox" name="income[]" value="other" {{ in_array('other', old('income', [])) ? 'checked' : '' }}> أخرى:</label>
        <input type="text" name="income_other" placeholder="اكتب هنا..." value="{{ old('income_other') }}">
      </section>

      <!-- 9. هيكل التكلفة -->
      <section>
        <h2>هيكل التكلفة</h2>
        <p>ما هي التكاليف الأساسية لتشغيل المشروع؟</p>

        <label>العملة: <span style="color: red;">*</span></label>
        <select id="currencySelect" name="currency" required>
          <option value="$" {{ old('currency') == '$' ? 'selected' : '' }}>دولار أمريكي ($)</option>
          <option value="₺" {{ old('currency') == '₺' ? 'selected' : '' }}>ليرة تركية (₺)</option>
          <option value="ل.س" {{ old('currency') == 'ل.س' ? 'selected' : '' }}>ليرة سورية (ل.س)</option>
        </select>

        <div class="cost-table-container">
          <table class="cost-table">
            <thead>
              <tr>
                <th>نوع المصروف</th>
                <th>الوصف</th>
                <th>التكلفة (<span class="currency-symbol">$</span>)</th>
                <th>الكمية</th>
                <th>المجموع (<span class="currency-symbol">$</span>)</th>
              </tr>
            </thead>
            <tbody id="costTableBody">
              <tr>
                <td><input type="text" placeholder="مثال: مواد خام" name="expense_type_1" value="{{ old('expense_type_1') }}"></td>
                <td><input type="text" placeholder="..." name="expense_desc_1" value="{{ old('expense_desc_1') }}"></td>
                <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_1" class="cost-input" data-row="1" value="{{ old('expense_cost_1') }}"></td>
                <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_1" class="qty-input" data-row="1" value="{{ old('expense_qty_1') }}"></td>
                <td class="total-cell"><span class="row-total" data-row="1">۰</span></td>
              </tr>
              <tr>
                <td><input type="text" placeholder="مثال: أجور عمالة" name="expense_type_2" value="{{ old('expense_type_2') }}"></td>
                <td><input type="text" placeholder="..." name="expense_desc_2" value="{{ old('expense_desc_2') }}"></td>
                <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_2" class="cost-input" data-row="2" value="{{ old('expense_cost_2') }}"></td>
                <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_2" class="qty-input" data-row="2" value="{{ old('expense_qty_2') }}"></td>
                <td class="total-cell"><span class="row-total" data-row="2">۰</span></td>
              </tr>
              <tr>
                <td><input type="text" placeholder="مثال: إيجار" name="expense_type_3" value="{{ old('expense_type_3') }}"></td>
                <td><input type="text" placeholder="..." name="expense_desc_3" value="{{ old('expense_desc_3') }}"></td>
                <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_3" class="cost-input" data-row="3" value="{{ old('expense_cost_3') }}"></td>
                <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_3" class="qty-input" data-row="3" value="{{ old('expense_qty_3') }}"></td>
                <td class="total-cell"><span class="row-total" data-row="3">۰</span></td>
              </tr>
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
        <button type="submit">إنشاء نموذج العمل</button>
        <button type="reset">مسح الحقول</button>
      </div>

    </form>
  </main>
@endsection

@push('scripts')
  <script>
    let rowCounter = 3;

    // Add suggestion to textarea
    function addSuggestion(fieldName, text) {
      const textarea = document.querySelector(`[name="${fieldName}"]`);
      const currentValue = textarea.value.trim();
      if (currentValue) {
        textarea.value = currentValue + '، ' + text;
      } else {
        textarea.value = text;
      }
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
      // Convert to Western for validation
      let westernValue = toWesternNumerals(value);
      // Keep only numbers and decimal point
      westernValue = westernValue.replace(/[^\d.]/g, '');
      // Ensure only one decimal point
      const parts = westernValue.split('.');
      if (parts.length > 2) {
        westernValue = parts[0] + '.' + parts.slice(1).join('');
      }
      // Convert back to Arabic numerals for display
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

        // Convert Arabic numerals to Western for calculation
        const costValue = toWesternNumerals(costInput.value);
        const qtyValue = toWesternNumerals(qtyInput.value);

        const cost = parseFloat(costValue) || 0;
        const qty = parseFloat(qtyValue) || 0;
        const rowTotal = cost * qty;

        // Display in Arabic numerals
        totalSpan.textContent = toArabicNumerals(rowTotal.toFixed(2));
        grandTotal += rowTotal;
      });

      // Display grand total in Arabic numerals
      document.getElementById('grandTotal').textContent = toArabicNumerals(grandTotal.toFixed(2));
    }

    // Add new row to cost table
    function addCostRow() {
      rowCounter++;
      const tbody = document.getElementById('costTableBody');
      const newRow = document.createElement('tr');

      newRow.innerHTML = `
        <td><input type="text" placeholder="نوع المصروف" name="expense_type_${rowCounter}"></td>
        <td><input type="text" placeholder="..." name="expense_desc_${rowCounter}"></td>
        <td><input type="text" inputmode="decimal" placeholder="۰" name="expense_cost_${rowCounter}" class="cost-input" data-row="${rowCounter}"></td>
        <td><input type="text" inputmode="numeric" placeholder="۰" name="expense_qty_${rowCounter}" class="qty-input" data-row="${rowCounter}"></td>
        <td class="total-cell"><span class="row-total" data-row="${rowCounter}">۰</span></td>
      `;

      tbody.appendChild(newRow);
      attachCalculationListeners(newRow);
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

    // Initialize calculation listeners on page load
    document.addEventListener('DOMContentLoaded', function() {
      attachCalculationListeners(document);

      // Add currency change listener
      document.getElementById('currencySelect').addEventListener('change', updateCurrencySymbol);

      // Initialize currency symbol on load
      updateCurrencySymbol();

      // Calculate totals if there are old values
      calculateTotals();
    });
  </script>
@endpush
