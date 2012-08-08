package com.wikia.webdriver.pageObjects.PageObject;

import java.util.Date;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.annotations.BeforeClass;



public class BasePageObject{

	public final WebDriver driver;
	
	public String liveDomain = "http://www.wikia.com/";
	
	public String wikiFactoryLiveDomain = "http://community.wikia.com/wiki/Special:WikiFactory";
	
	public String userName = "KarolK1";
	public String password = "123";
	
	public String userNameStaff = "KarolK";
	public String passwordStaff = "123";
	
	protected int timeOut = 30;
	
	public WebDriverWait wait;

	
	public BasePageObject(WebDriver driver)
	{
		this.driver = driver;
		wait = new WebDriverWait(driver, timeOut);
		driver.manage().window().maximize();
	}
	

	/**
	 * Checks page title
	 *
	 ** @param title Specifies the title that you want to compare with the actual current title
	 */

	public boolean verifyTitle(String title)
	{
		String currentTitle = driver.getTitle();
		if (!currentTitle.equals(title))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Checks if the current URL contains the given String
	 *
	 *  @author Michal Nowierski
	 ** @param GivenString 
	 */
	public boolean verifyURLcontains(String GivenString)
	{
		String currentURL = driver.getCurrentUrl();
		if (currentURL.contains(GivenString))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Checks if the current URL is the given URL
	 *
	 *  @author Michal Nowierski
	 ** @param GivenURL 
	 */
	public boolean verifyURL(String GivenURL)
	{
		String currentURL = driver.getCurrentUrl();
		if (currentURL.equals(GivenURL))
		{
			return true;
		}
		return false;
	}
	
	
	/**
	 * Clicks on an element
	 */

	public void click(WebElement pageElem)
	{
		pageElem.click();
	}
	
	/**
	 * Checks if the element is visible on browser
	 *
	 ** @param element The element to be checked
	 */
	public void waitForElementByElement(WebElement element)
	{
		wait.until(ExpectedConditions.visibilityOf(element));
	}
	
	public void waitForElementByCss(String cssSelector)
	{
		
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.cssSelector(cssSelector)));
	}
	
	public void waitForElementByClassName(String className)
	{
		
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className(className)));
	}
	
	public void waitForElementByClass(String id)
	{
		
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id(id)));
	}
	
	public void waitForElementByXPath(String xPath)
	{
		
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath(xPath)));
	}
	
	
	
	public void waitForElementNotVisibleByCss(String css)
	{

		wait.until(ExpectedConditions.invisibilityOfElementLocated(By.cssSelector(css)));
	}
	
	public void waitForElementClickableByClassName(String className)
	{
		wait.until(ExpectedConditions.elementToBeClickable(By.className(className)));
	}
	
	public void waitForElementClickableByCss(String css)
	{
		wait.until(ExpectedConditions.elementToBeClickable(By.cssSelector(css)));
	}
	
	public void waitForElementById(String id)
	{
		
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id(id)));
	}
	
	/**
	 * Navigates back to the previous page 
	 */
	public void navigateBack() {
		driver.navigate().back();
	}
	
	
	
	
	public String getTimeStamp()
	{
		Date time = new Date();
		long timeCurrent = time.getTime();
		return String.valueOf(timeCurrent);
		
	}
	
	
	

	
    
} 