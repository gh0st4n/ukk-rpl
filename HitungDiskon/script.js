// Fungsi untuk membersihkan input agar hanya angka
function cleanNumber(input) {
  if (!input) return 0;
  return parseFloat(input.replace(/[^0-9]/g, "")) || 0;
}

function formatRupiah(num) {
  return num.toLocaleString("id-ID");
}

function calculateDiscount() {
  const itemName = document.getElementById("itemName").value || "Barang";
  const priceInput = document.getElementById("price").value;
  const discountType = document.getElementById("discountType").value;
  const discountInput = document.getElementById("discountValue").value;
  const resultDiv = document.getElementById("result");

  const price = cleanNumber(priceInput);
  const discountValue = cleanNumber(discountInput);

  if (price <= 0 || discountValue < 0) {
    resultDiv.style.display = "block";
    resultDiv.innerHTML = "⚠️ Masukkan harga dan diskon dengan benar!";
    return;
  }

  let discountAmount = 0;
  let finalPrice = price;

  if (discountType === "percent") {
    discountAmount = (price * discountValue) / 100;
    finalPrice = price - discountAmount;
  } else if (discountType === "nominal") {
    discountAmount = discountValue;
    finalPrice = price - discountAmount;
  }

  if (finalPrice < 0) finalPrice = 0;

  resultDiv.style.display = "block";
  resultDiv.innerHTML = `
    🛒 <strong>${itemName}</strong><br>
    💵 Harga Awal: Rp${formatRupiah(price)} <br>
    🔖 Diskon: ${discountType === "percent" ? discountValue + "%" : "Rp" + formatRupiah(discountValue)} <br>
    ➖ Potongan Harga: Rp${formatRupiah(discountAmount)} <br>
    ✅ Harga Akhir: <span style="color:green">Rp${formatRupiah(finalPrice)}</span>
  `;
}

