package com.wikia.selenium.tests;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import org.testng.annotations.Test;

import static org.testng.AssertJUnit.assertFalse;
import static org.testng.AssertJUnit.assertTrue;
import static org.testng.AssertJUnit.assertEquals;

import java.util.List;

public class WhatIsMyIpTest extends BaseTest {
	@Test(groups={"CI", "legacy"})
	public void testAssertThatInfrastructureIPsAreNotPresented() throws Exception {
		openAndWait("index.php?title=Special:WhatIsMyIP");

		// check what page you land on
		waitForElement("//header[@id='WikiaPageHeader']/h1");
		assertTrue(session().getLocation().contains("index.php?title=Special:WhatIsMyIP"));
		assertEquals(session().getText("//header[@id='WikiaPageHeader']/h1"), "What is my IP");
		// and invalid IP addresses
		String[] ips = getTestConfig().getStringArray("ci.extension.wikia.WhatIsMyIP.ip");
		assertTrue(ips.length > 0);
		for (int i = ips.length - 1; i >= 0; i--) {
			assertFalse(ips[i] + " is present", session().isTextPresent(ips[i]));
		}
	}
}
