package com.wikia.selenium.tests;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static org.testng.AssertJUnit.assertFalse;
import static org.testng.AssertJUnit.assertTrue;

import org.testng.annotations.Test;

public class ToolbarTest extends BaseTest {

	public static String randomArticlePath = "index.php?title=Special:Random";
	
	/**
	 * selenium training test
	 */ 
	@Test
	public void testEditToolbarEntry() throws Exception {
		
		openAndWait(randomArticlePath);
		login();
		logout();
	}
	
	protected boolean isActionAdded(String actionId) throws Exception {
		return session().isElementPresent("css=section#MyToolsConfigurationWrapper li[data-tool-id=\"" + actionId + "\"]");
	}
	
	protected void addActionToToolbar(String actionId) throws Exception {
		if (!session().isVisible("css=section#MyToolsConfigurationWrapper ul.popular-list")) {
			session().click("css=section#MyToolsConfigurationWrapper div.popular-toggle.toggle-1");
			waitForElementVisible("css=section#MyToolsConfigurationWrapper ul.popular-list");
		}
		session().click("css=section#MyToolsConfigurationWrapper a[data-tool-id=\"" + actionId + "\"]");
		assertTrue(isActionAdded(actionId));
	}
	
	protected void removeActionFromToolbar(String actionId) throws Exception {
		session().click("css=section#MyToolsConfigurationWrapper li[data-tool-id=\"" + actionId + "\"] img.trash");
	}
	
	/*
	protected void moveAction(String actionId, String targetActionId) throws Exception {
		session().mouseMoveAt("css=li[data-tool-id=\"" + actionId + "\"] img.drag", "");
		session().mouseDownAt("css=li[data-tool-id=\"" + actionId + "\"] img.drag", "");
		session().mouseMoveAt("css=li[data-tool-id=\"" + targetActionId + "\"]", "");
		session().mouseUpAt("css=li[data-tool-id=\"" + targetActionId + "\"]", "");
	}
	
	protected void moveAction2(String actionId, String movement) throws Exception {
		session().dragAndDrop("css=li[data-tool-id=\"" + actionId + "\"] img.drag", movement);
	}
	
	@Test(groups={"envProduction","legacy","manual"})
	public void testMoveToToolbar() throws Exception {
		openAndWait("/");
		login();
		session().click("css=#WikiaFooter a.tools-customize");
		waitForElement("css=section#MyToolsConfigurationWrapper span.reset-defaults");
		addActionToToolbar("PageAction:Move");
		removeActionFromToolbar("PageAction:Move");
		Thread.sleep(10000);
		logout();		
	}
	*/
	
	@Test(groups={"envProduction","legacy"})
	public void testEnsuresThatToolbarIsNotPresentForAnonymousUsers() throws Exception {
		//Written by Aga Serowiec 02-Feb-2012
		openAndWait("/");
		assertTrue(session().isElementPresent("WikiaFooter"));
		assertFalse(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]"));
	}

	@Test(groups={"envProduction","legacy"})
	public void testResetsDefaultsInCustomizedToolbar() throws Exception {
		//Written by Aga Serowiec 02-Feb-2012
		openAndWait("/");
		login();
		session().click("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='customize']");
		waitForElement("css=section#MyToolsConfigurationWrapper span.reset-defaults");
		session().click("css=section#MyToolsConfigurationWrapper span.reset-defaults a");

		assertFalse(session().isElementPresent("//section[contains(@class, 'modalContent')]//ul[contains(@class, 'options-list ui-sortable')]//li[@data-caption='Edit']"));
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'buttons')]/input[@type='submit']");
		logout();
		
		loginAsRegular();
		session().click("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='customize']");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']");
		session().click("//section[@id='MyToolsConfigurationWrapper']//a[@href='#']");
		assertFalse(session().isElementPresent("//section[contains(@class, 'modalContent')]//ul[contains(@class, 'options-list ui-sortable')]//li[@data-caption='Edit']"));
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'buttons')]/input[@type='submit']");
		logout();
		
	}
	
	
	@Test(groups={"envProduction","legacy"},dependsOnMethods={"testResetsDefaultsInCustomizedToolbar"},alwaysRun=false)
	public void testEnsuresThatSignedInUserCanAddAnItemToCustomizedToolbar() throws Exception {
		//Written by Aga Serowiec 02-Feb-2012
		
		openAndWait("/");	
		login();
		assertTrue(session().isElementPresent("WikiaFooter"));
		
		//"ul.tools a.tools-customize"
		
		assertTrue(session().isElementPresent("footer#WikiaFooter div.toolbar"));
		//assertTrue(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]"));
		
		//footer#WikiaFooter div.toolbar
		session().click("ul.tools link=Customize");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']");
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'popular-toggle toggle-1')]");
		waitForElementVisible("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'popular-list')]");
		session().click("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'popular-list')]//a[@data-tool-id='PageAction:Edit']");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'options-list ui-sortable')]//li[@data-tool-id='PageAction:Edit']");
		assertTrue(session().isVisible("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'options-list ui-sortable')]//li[@data-tool-id='PageAction:Edit']"));
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'buttons')]/input[@type='submit']");
		waitForElement("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']");
		assertTrue(session().isVisible("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']"));
		logout();
		
		loginAsRegular();
		assertFalse(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']"));
		logout();
		
		login();
		assertTrue(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']"));
		logout();
		
		
		openAndWait("/");
		loginAsRegular();
		openAndWait("wiki/Special:Upload");
		session().click("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='customize']");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']");
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'popular-toggle toggle-1')]");
		waitForElementVisible("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'popular-list')]");
		session().click("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'popular-list')]//a[@data-tool-id='PageAction:Edit']");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'options-list ui-sortable')]//li[@data-tool-id='PageAction:Edit']");
		assertTrue(session().isVisible("//section[@id='MyToolsConfigurationWrapper']//ul[contains(@class, 'options-list ui-sortable')]//li[@data-tool-id='PageAction:Edit']"));
		session().click("//section[@id='MyToolsConfigurationWrapper']//div[contains(@class, 'buttons')]/input[@type='submit']");
		waitForElementNotPresent("//section[@id='MyToolsConfigurationWrapper']");
		waitForElement("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]");
		assertFalse(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']"));
		
		editArticle("toolbartest", "testujemy toolbar");
		waitForElement("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]");
		assertTrue(session().isVisible("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='edit']"));
		logout();
	}
	@Test(groups={"envProduction","legacy"})
	public void testEnsuresThatSignedInUserCanSearchFindAndAddAnItemToCustomizedToolbar() throws Exception {
		//Written by Rodrigo 11-Apr-2012
		openAndWait("/");
		login();
		assertTrue(session().isElementPresent("//footer[@id='WikiaFooter']/div[@class='toolbar']"));
		
		session().click("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='customize']");
		waitForElement("//section[@id='MyToolsConfigurationWrapper']");
		session().click("//input[@class='search']");
		session().type("//div[@id='MyToolsConfiguration']/form/div[2]/div/input", "up");		
		session().keyUp("//div[@id='MyToolsConfiguration']/form/div[2]/div/input", "s");
		
		assertTrue(session().isVisible("//div[@title='Upload photo']"));
		session().click("//div[@title='Upload photo']");
		assertTrue(session().isElementPresent("//div[@id='MyToolsConfiguration']//li[@data-caption='Upload photo']"));
		
		session().click("//form[@class='toolbar-customize']//input[@class='save-button']");
		assertTrue(session().isVisible("//div[@class='toolbar']//a[@data-name='upload']"));
		
		logout();
		
		loginAsRegular();
		assertFalse(session().isElementPresent("//footer[@id='WikiaFooter']//div[contains(@class, 'toolbar')]//a[@data-name='upload']"));
		logout();
		
		login();
		assertTrue(session().isVisible("//div[@class='toolbar']//a[@data-name='upload']"));
		logout();
		
	}	
//@Test(groups={"envProduction","legacy"},dependsOnMethods={"testResetsDefaultsInCustomizedToolbar"},alwaysRun=false)
	//@Test(groups={"envProduction","legacy"})
	//public void testVerifiesThatSignedInUserCanDeleteAnItemInCustomizedToolbar() throws Exception {
		//WIP Written by Patrick Archbold 10-Apr-2012
		
		
		/*openAndWait(randomArticlePath);
		login();
		
		assertTrue(session().isElementPresent("WikiaFooter"));
		
		
		
		assertTrue(session().isElementPresent("footer#WikiaFooter div.toolbar"));
				
		
		session().click("ul.tools a.tools-customize");
	
		assertTrue(session().isElementPresent("section#MyToolsConfigurationWrapper"));
		session().click("div.popular-toggle.toggle-1");
		
		session().click("ul.popular-list a.PageAction:Edit");
				session().click("input.save-button");
		assertTrue(session().isElementPresent("ul.popular-list a.PageAction:Edit"));
		session().click("ul.tools a.tools-customize");
		
				
		logout();
		
		
		
		
		
	
	}*/
}