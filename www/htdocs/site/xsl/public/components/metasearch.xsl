<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:include href="default/metasearch.xsl"/>  
	 <xsl:template match="metasearch">
                <div id="search" style="display: block;">
                        <h3>
                                <span><xsl:value-of select="text[@id = 'search_title']" /></span>
                        </h3>
                        <form name="searchForm" action="#" method="post" onsubmit="return(executeSearch());">
                                <input type="hidden" name="lang"   value="{$lang}" />
                                <input type="hidden" name="engine" value="metaiah"/>
                                <input type="hidden" name="group"  value="&lt;?= $_REQUEST['id'] ?&gt;"/>
                                <input type="hidden" name="view"   value="&lt;?= $def['RESULT'] ?&gt;"/>

                                <div class="searchItens">
                                        <xsl:apply-templates select="text[@id = 'search_entryWords']" /><br />
                                        <input type="text" name="expression" class="expression" />
                                        <input type="submit" value="{text[@id = 'search_submit']}" name="submit" class="submit" /><br />
										
                                </div>
                        </form>
				<span class="advancedSearch"><a href="/site/metaiah/search.php?lang={$lang}&amp;form=advanced"><xsl:apply-templates select="text[@id = 'search_advancedSearch']" /></a></span>
                </div>

       			

        </xsl:template>
	
	

</xsl:stylesheet>