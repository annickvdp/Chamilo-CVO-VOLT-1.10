﻿if (typeof AMprocessNode!='function'){LoadScript(FCKConfig.ScriptASCIIMathML);};var notifyIfNoMathML=false;var alertIfNoMathML=false;var notifyIfNoSVG=false;var alertIfNoSVG=false;var translateASCIIMath=false;FCKCommands.RegisterCommand('asciisvg',new FCKDialogCommand(FCKLang['DlgAsciiSvg'],FCKLang['DlgAsciiSvgGraphEditor'],FCKConfig.PluginsPath+'asciisvg/fck_asciisvg.html',750,550));var oAsciiSvgItem=new FCKToolbarButton('asciisvg',FCKLang['DlgAsciiSvg']);oAsciiSvgItem.IconPath=FCKConfig.PluginsPath+'asciisvg/asciisvg.gif';FCKToolbarItems.RegisterItem('asciisvg',oAsciiSvgItem);FCK.ContextMenu.RegisterListener({AddItems:function(A,B,C){if (B.tagName=='IMG'&&B.getAttribute('_fckasciisvg')){A.AddSeparator();A.AddItem('asciisvg',FCKLang['DlgAsciiSvg'],oAsciiSvgItem.IconPath);}}});FCK.RegisterDoubleClickHandler(function (A){if (A.tagName=='IMG'&&A.getAttribute('_fckasciisvg')){FCKCommands.GetCommand('asciisvg').Execute();}},null);var FCKAsciiSvg={};FCKAsciiSvg.UpdateLinkedField=FCK.UpdateLinkedField;FCK.UpdateLinkedField=function(){if (FCKConfig.FullPage){var A=FCK.EditorDocument.getElementsByTagName('html')[0];var B;var C;if (typeof A=='object'){B=A.getElementsByTagName('HEAD')[0];};if (typeof B=='object'){var D=FCK.GetData(false);var E=false;var F=false;if (D){if (D.toString().match(/<embed[^>]*sscr=[^>]*>/i)){E=true;};head_data=D.toString().match(/<head[^>]*>(.*?)<\/head\s*>/i);if (head_data&&head_data.toString().indexOf('ASCIIMathML.js')!=-1){F=true;}};if (E&&!F){script=FCK.EditorDocument.createElement('script');script.setAttribute('src',FCKConfig.ScriptASCIIMathML);script.setAttribute('type','text/javascript');B.appendChild(script);}}};FCKAsciiSvg.UpdateLinkedField();};
