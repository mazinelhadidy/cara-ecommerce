# مشروع لارفل إيكومرس - مهمة الأدمن (Admin Assignment)

أهلاً بيك يا بطل في مهمة تطوير لوحة التحكم (Admin Dashboard). المشروع ده معمول بـ Laravel ومحتاج منك شوية خطوات بسيطة عشان تبدأ تشتغل. ركز في الخطوات دي:

## 🚀 إزاي تشغل المشروع؟

اتبع الخطوات دي بالترتيب عشان السيرفر يشتغل عندك من غير مشاكل:

### 1. تظبيط ملف البيئة (Environment Setup)
أول حاجة، خد نسخة من ملف الـ `.env.example` وسميها `.env` وبعدين عمل generate للـ key:
```bash
cp .env.example .env
php artisan key:generate
```

### 2. ربط قاعدة البيانات (Database Connection)
افتح ملف الـ `.env` وظبط إعدادات الداتابيز بتاعتك:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=اسم_الداتابيز_بتاعتك
DB_USERNAME=root
DB_PASSWORD=
```
بعد ما تخلص، اعمل ميجريشن للجداول:
```bash
php artisan migrate
```

### 3. تحميل المكتبات (Install Dependencies)
حمل مكتبات PHP و JavaScript:
```bash
composer install
npm install
```

### 4. تشغيل المشروع
محتاج تفتح شاشتين Terminal وتشغل الأمرين دول مع بعض:

**الشاشة الأولى (Vite):**
```bash
npm run dev
```

**الشاشة الثانية (Laravel Server):**
```bash
php artisan serve
```

---

## ⚠️ ملاحظة مهمة جداً (بخصوص الـ Routing)

> [!IMPORTANT]
> فيه فولدر موجود في `public/` اسمه `admin` وده فيه ملفات الـ CSS والـ JS بتاعة التيمبلت. عشان كده، مينفعش نعمل "Route" اسمه `/admin` عشان هيحصل تعارض مع الفولدر ده والسيرفر مش هيفتح الصفحة.
>
> **عشان كدا، غيرنا الروت بتاع الأدمن من `/admin` وخليناه `/dashboard`.**
>
> يعني عشان تدخل على لوحة التحكم، استخدم اللينك ده: `http://127.0.0.1:8000/dashboard`

---

## 📝 التاسكات المطلوبة (Assignments)

مطلوب منك تنفذ شوية حاجات في المشروع ده عشان تكمله:

1. **CRUD للـ Categories**: تعمل إضافة، تعديل، مسح، وعرض للـ Categories من خلال الـ Dashboard.
2. **CRUD للـ Products**: تعمل إضافة، تعديل، مسح، وعرض للـ Products من خلال الـ Dashboard.
3. **عرض المنتجات في الـ Shop**: المنتجات اللي هتضيفها من الـ Dashboard، لازم تتعرض في صفحة الـ Shop (مش في الـ Dashboard).
4. **خاصية الـ Add to Cart**: تظبط الزرار والمنطق البرمجي لإضافة منتج للعربة.
5. **Bonus**: أي ميزة إضافية حابب تزودها (زي الـ Search، أو الـ Filter، أو الـ Checkout) هتحسبلك Bonus طبعاً.

---

## 🛠️ لو واجهتك مشكلة (Troubleshooting)

لو حسيت إن فيه حاجة مش شغالة، أو الصفحات مش بتفتح صح، استخدم الأمر ده عشان تمسح أي "Cache" قديم:

```bash
php artisan optimize:clear
```

الأمر ده بينضف كل الحاجات المتخزنة (Config, Route, View) وبيرجع الدنيا فريش تاني.

---

بالتوفيق يا بطل! 💻
