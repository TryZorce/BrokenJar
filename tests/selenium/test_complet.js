const { By, Key, Builder, until } = require("selenium-webdriver");
require('chromedriver');  // Import chromedriver

(async function registrationTest() {
    let driver = await new Builder().forBrowser("chrome").build();

    try {
        console.log("Ouverture de la page de registration");
        await driver.get('http://localhost:3000/register');
        await driver.manage().window().setRect({ width: 1920, height: 995 });

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie de l'email");
        let emailInput = await driver.wait(until.elementLocated(By.name('email')), 10000);
        await emailInput.click();
        await emailInput.sendKeys('test@gmail.com');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie du mot de passe");
        let passwordInput = await driver.findElement(By.name('password'));
        await passwordInput.sendKeys('azerty');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie du nom");
        let nameInput = await driver.findElement(By.name('name'));
        await nameInput.sendKeys('test');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie du prénom");
        let firstnameInput = await driver.findElement(By.name('firstname'));
        await firstnameInput.sendKeys('test');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie de l'adresse");
        let addressInput = await driver.findElement(By.name('address'));
        await addressInput.sendKeys('test adresse');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie du téléphone");
        let phoneInput = await driver.findElement(By.name('phone'));
        await phoneInput.click();
        await phoneInput.sendKeys('0601020304');

        await driver.sleep(1000); // Délai ajouté

        console.log("Saisie du numéro de carte");
        let cardInput = await driver.findElement(By.name('card'));
        await cardInput.click();
        await cardInput.sendKeys('9264896672503037');

        await driver.sleep(1000); 

        console.log("Saisie du cryptogramme");
        let cryptoInput = await driver.findElement(By.name('crypto'));
        await cryptoInput.click();
        await cryptoInput.sendKeys('948');

        await driver.sleep(1000);

        console.log("Saisie de la date d'expiration");
        let expiryInput = await driver.findElement(By.name('expiry'));
        await expiryInput.sendKeys('11/25');

        await driver.sleep(1000);

        console.log("Clic sur le bouton d'inscription");
        let signupButton = await driver.findElement(By.css('.text-blue-500'));
        await signupButton.click();

        await driver.sleep(2000);

        console.log("Connexion avec les nouvelles informations d'utilisateur");
        let usernameInput = await driver.wait(until.elementLocated(By.name('username')), 10000);
        await usernameInput.click();
        await usernameInput.sendKeys('test@gmail.com');

        let loginPasswordInput = await driver.findElement(By.name('password'));
        await loginPasswordInput.sendKeys('azerty');

        await driver.sleep(1000);

        let loginButton = await driver.findElement(By.css('.px-4'));
        await loginButton.click();

        await driver.sleep(2000);

        console.log("Recherche et saisie d'un numéro de contravention");
        let fineNumberInput = await driver.wait(until.elementLocated(By.name('fineNumber')), 10000);
        await fineNumberInput.click();
        await fineNumberInput.sendKeys('KW2024_22_78');

        await driver.sleep(1000);

        let searchButton = await driver.findElement(By.css('.mt-6:nth-child(3)'));
        await searchButton.click();

        await driver.sleep(2000); 

        await driver.executeScript('window.scrollTo(0,0)');

        console.log("Retour au profil utilisateur");
        let profileLink = await driver.wait(until.elementLocated(By.linkText('Retour au profil')), 10000);
        await profileLink.click();

        await driver.sleep(1000);

        console.log("Recherche et saisie d'un autre numéro de contravention");
        fineNumberInput = await driver.findElement(By.name('fineNumber'));
        await fineNumberInput.click();
        await fineNumberInput.sendKeys('AB2024_24_76');


        let Test = await driver.findElement(By.css('.mt-6:nth-child(3)'));
        await Test.click();

        await driver.sleep(1000); 

        let Validate = await driver.findElement(By.css('.mt-6.bg-blue-500.text-white.px-4.py-2.rounded'));
        await Validate.click();

        await driver.sleep(1000); 

        console.log("Test complet terminé avec succès");
    } catch (e) {
        console.error(e);
    } finally {
        await driver.quit();
        console.log("Navigateur fermé");
    }
}());
