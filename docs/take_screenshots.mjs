import puppeteer from 'puppeteer';

(async () => {
  const browser = await puppeteer.launch({
    headless: "new",
    ignoreHTTPSErrors: true
  });
  const page = await browser.newPage();

  // Set to mobile viewport
  await page.setViewport({ width: 400, height: 800, isMobile: true, hasTouch: true });

  console.log("Navigating to login page...");
  await page.goto('https://qrbootrap.test/login', { waitUntil: 'networkidle2' });

  console.log("Logging in...");
  await page.type('input[name="login_id"]', 'demo');
  await page.type('input[name="password"]', '12345678');
  await page.click('button[type="submit"]');
  await page.waitForNavigation({ waitUntil: 'networkidle2' });

  const fs = await import('fs');
  if (!fs.existsSync('public/screenshots')) {
    fs.mkdirSync('public/screenshots');
  }

  // 1. Dashboard
  console.log("Navigating to dashboard...");
  await page.goto('https://qrbootrap.test/dashboard', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: `public/screenshots/dashboard.png` });

  // 2. Menu
  console.log("Opening menu...");
  try {
    await page.click('#sidebarToggle');
    await new Promise(res => setTimeout(res, 1000));
    await page.screenshot({ path: `public/screenshots/dashboard-menu.png` });
    await page.click('#sidebarToggle'); // close it
  } catch (e) {
    console.log("Could not click sidebar toggle");
  }

  const routes = [
    { url: 'production/scan-mobile', name: 'scan-mobile' },
    { url: 'production/coating-confirmation', name: 'coating-confirmation' },
    { url: 'production/coating-update', name: 'coating-update' },
    { url: 'warehouse/scan-to-location', name: 'warehouse' }
  ];

  for (let r of routes) {
    console.log(`Navigating to ${r.url}...`);
    await page.goto(`https://qrbootrap.test/${r.url}`, { waitUntil: 'networkidle2' });
    await new Promise(res => setTimeout(res, 1000));

    // Empty form screenshot
    await page.screenshot({ path: `public/screenshots/${r.name}-empty.png` });

    // Try to find first text input and type something
    try {
      const inputs = await page.$$('input[type="text"], input[type="search"]');
      if (inputs.length > 0) {
        await inputs[0].type('123456789');
        await new Promise(res => setTimeout(res, 500));
        await page.screenshot({ path: `public/screenshots/${r.name}-input.png` });
      }
    } catch (e) {
      console.log(`Could not type into ${r.name}`);
    }

    console.log(`Saved screenshots for ${r.name}`);
  }

  await browser.close();
  console.log("Done!");
})();
