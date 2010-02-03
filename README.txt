example TypoScript


//merken link
page.10.marks.BOOKMARK_LINK < plugin.tx_mnpagebookmark_pi1
page.10.marks.BOOKMARK_LINK {
	mode = 1
}




plugin.tx_mnpagebookmark_pi1 {
	validateBMListOnView{
		whitDB = 0
		//muss null bleiben is noch fehler behaftet und auch nicht nötig ;)
		
		whitCookie = 0
	}
	
	templateFile = EXT:mn_bookmark/res/BookmarkTemplateFile.html
		
	URLparamter {
		stdWrap.crop = 58|...
		1 = RECORDS
		1 {
				getvar = tx_myext_pi1[test]
				source = 1
				tables = tx_myext_test
				conf.tx_myext_testy = TEXT
				conf.tx_myext_test.field = name
				conf.tx_myext_test.wrap = |
				
		}
		
				
	}
}