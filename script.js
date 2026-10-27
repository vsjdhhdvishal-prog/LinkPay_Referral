const linkInput = document.getElementById("refLink");
const message = document.getElementById("message");
const activity = document.getElementById("activity");

const DEMO_REWARD_PER_REFERRAL = 10;
const STORAGE_KEY = "linkpay_demo_referrals";

function getReferrals() {
  try {
    const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    return Array.isArray(data) ? data : [];
  } catch {
    return [];
  }
}

function saveReferrals(data) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function referralLink() {
  const base = window.location.href.split("?")[0];
  return base + "?ref=DEMO" + Math.random().toString(36).slice(2, 7);
}

linkInput.value = referralLink();

function render() {
  const referrals = getReferrals();
  document.getElementById("referrals").textContent = referrals.length;

  const demoReward = referrals.length * DEMO_REWARD_PER_REFERRAL;
  document.getElementById("reward").textContent =
    "₹" + demoReward.toLocaleString("en-IN");

  if (referrals.length === 0) {
    activity.innerHTML = '<p class="empty">अभी कोई डेमो गतिविधि नहीं है।</p>';
    return;
  }

  activity.replaceChildren();

  referrals.slice().reverse().forEach((item) => {
    const row = document.createElement("div");
    row.className = "activity-item";

    const details = document.createElement("div");
    const name = document.createElement("b");
    name.textContent = "Demo Referral";
    const date = document.createElement("small");
    date.textContent = item.date;
    date.style.display = "block";

    const reward = document.createElement("strong");
    reward.textContent = "+₹10 (Demo)";

    details.append(name, date);
    row.append(details, reward);
    activity.append(row);
  });
}

document.getElementById("copyBtn").addEventListener("click", async () => {
  try {
    await navigator.clipboard.writeText(linkInput.value);
    message.textContent = "Demo link copied!";
  } catch {
    linkInput.select();
    message.textContent = "Link select हो गया है — Copy दबाएँ।";
  }
});

document.getElementById("shareBtn").addEventListener("click", async () => {
  const data = {
    title: "LinkPay Demo",
    text: "यह LinkPay referral website का demo है।",
    url: linkInput.value
  };

  if (navigator.share) {
    try {
      await navigator.share(data);
    } catch {
      message.textContent = "Share cancel किया गया।";
    }
  } else {
    try {
      await navigator.clipboard.writeText(linkInput.value);
      message.textContent = "Demo link copied!";
    } catch {
      message.textContent = "ऊपर दिए लिंक को कॉपी करें।";
    }
  }
});

document.getElementById("demoBtn").addEventListener("click", () => {
  const referrals = getReferrals();

  referrals.push({
    date: new Date().toLocaleString("hi-IN")
  });

  saveReferrals(referrals);
  render();
  message.textContent = "Demo referral जोड़ा गया। कोई वास्तविक भुगतान नहीं हुआ।";
});

document.getElementById("resetBtn").addEventListener("click", () => {
  if (confirm("क्या आप सभी demo records हटाना चाहते हैं?")) {
    localStorage.removeItem(STORAGE_KEY);
    render();
    message.textContent = "Demo records reset हो गए।";
  }
});

render();
