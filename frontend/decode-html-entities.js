#!/usr/bin/env node
// Post-processes public/index.html to decode HTML entities for non-ASCII characters
// back to their UTF-8 equivalents, since pug auto-encodes them in attribute values.

const fs = require('fs');
const path = require('path');

const namedEntities = {
  '&Agrave;': 'À', '&agrave;': 'à',
  '&Aacute;': 'Á', '&aacute;': 'á',
  '&Acirc;': 'Â', '&acirc;': 'â',
  '&Atilde;': 'Ã', '&atilde;': 'ã',
  '&Auml;': 'Ä', '&auml;': 'ä',
  '&Aring;': 'Å', '&aring;': 'å',
  '&AElig;': 'Æ', '&aelig;': 'æ',
  '&Ccedil;': 'Ç', '&ccedil;': 'ç',
  '&Egrave;': 'È', '&egrave;': 'è',
  '&Eacute;': 'É', '&eacute;': 'é',
  '&Ecirc;': 'Ê', '&ecirc;': 'ê',
  '&Euml;': 'Ë', '&euml;': 'ë',
  '&Igrave;': 'Ì', '&igrave;': 'ì',
  '&Iacute;': 'Í', '&iacute;': 'í',
  '&Icirc;': 'Î', '&icirc;': 'î',
  '&Iuml;': 'Ï', '&iuml;': 'ï',
  '&ETH;': 'Ð', '&eth;': 'ð',
  '&Ntilde;': 'Ñ', '&ntilde;': 'ñ',
  '&Ograve;': 'Ò', '&ograve;': 'ò',
  '&Oacute;': 'Ó', '&oacute;': 'ó',
  '&Ocirc;': 'Ô', '&ocirc;': 'ô',
  '&Otilde;': 'Õ', '&otilde;': 'õ',
  '&Ouml;': 'Ö', '&ouml;': 'ö',
  '&Oslash;': 'Ø', '&oslash;': 'ø',
  '&Ugrave;': 'Ù', '&ugrave;': 'ù',
  '&Uacute;': 'Ú', '&uacute;': 'ú',
  '&Ucirc;': 'Û', '&ucirc;': 'û',
  '&Uuml;': 'Ü', '&uuml;': 'ü',
  '&Yacute;': 'Ý', '&yacute;': 'ý',
  '&THORN;': 'Þ', '&thorn;': 'þ',
  '&szlig;': 'ß',
  '&yuml;': 'ÿ',
  '&OElig;': 'Œ', '&oelig;': 'œ',
  '&Scaron;': 'Š', '&scaron;': 'š',
  '&Zcaron;': 'Ž', '&zcaron;': 'ž',
};

const file = path.resolve(__dirname, '../public/index.html');
let content = fs.readFileSync(file, 'utf8');

const pattern = new RegExp(
  Object.keys(namedEntities).map(e => e.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|'),
  'g'
);
content = content.replace(pattern, match => namedEntities[match] || match);

// Decode numeric entities for non-ASCII codepoints (> 127)
content = content.replace(/&#(\d+);/g, (match, n) => {
  const cp = parseInt(n, 10);
  return cp > 127 ? String.fromCodePoint(cp) : match;
});
content = content.replace(/&#x([0-9a-fA-F]+);/g, (match, h) => {
  const cp = parseInt(h, 16);
  return cp > 127 ? String.fromCodePoint(cp) : match;
});

fs.writeFileSync(file, content, 'utf8');
console.log('HTML entities decoded in public/index.html');
