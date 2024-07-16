<?php
require_once __DIR__ . '/local.php';

class Config extends LocalConfig {

    // Word Matching Arrays
    const WORD_MATCH_POSITIVE = ['edu','k12','isd','school','academy','daltonstate','loyola','mnps','snhu','calstatela','umbc','bcps','fordham','teachwesternma','mnps'];
    const WORD_MATCH_NEGATIVE = ['helpful','mom','resource','blog','conscious','positive','print','worksheets','allowed','hello','drama','atena','kids','feel','free','font','class','news','learning','shop','kinder','sisters','crave','your','ghost','happy','transform','collaborative','cafe','creature','teach','test','enrichment','early','center','teacher','miss','mrs','chick','stamps','wonderful','style','primary','elementary','secondary','college','coach','k12','school','academy','infant','thoughts','classroom','room','staff','match'];
    const WORD_MATCH_BLOCKED = ['amcik','arse','arsehole','arserape','arsewipe','a$$','asses','a$$e$','asshole','a$$hole','assholes','a$$hole$','assramer','assrape','atouche','ayir','b17ch','b1tch','bastard','beastial','beastiality','beastility','benchod','bestial','bestiality','bi7ch','bitch','bitcher','bitchers','bitches','bitchin','bitching','bloody','blowjob','blowjobs','boiolas','bollock','bollocks','boob','boobs','bugger','bullshit','butfuck','buttfuck','buttfucker','buttmonkey','c0ck','cabron','cawk','cazzo','chink','chraa','chuj','cipa','clamjouster','clit','clits','cock','cocking','cocks','cockslap','cockslapped','cockslapping','cocksuck','cocksucked','cocksucker','cocksucking','cocksucks','crap','cum','cummer','cumming','cums','cumshot','cunilingus','cunillingus','cunnilingus','cunt','cuntalot','cuntfish','cunting','cuntlick','cuntlicker','cuntlicking','cuntree','cunts','cyberfuc','cyberfuck','cyberfucked','cyberfucker','cyberfuckers','cyberfucking','d4mn','dago','damn','damnnation','daygo','dego','dick','dickabout','dickaround','dickhead','dicking','dickwad','dickward','dildo','dildos','dink','dinks','dirsa','dirty sanchez','donkey punch','douche','douchebag','dupa','dyke','dziwka','ejaculate','ejaculated','ejaculates','ejaculating','ejaculatings','ejaculation','ekrem','ekto','enculer','faen','fag','fagging','faggot','faggs','fagot','fagots','fags','fancul','fanny','fart','farted','farting','fartings','farts','farty','fatass','fcuk','feces','felatio','fellatio','ficken','fingerfuck','fingerfucked','fingerfucker','fingerfuckers','fingerfucking','fingerfucks','fistfuck','fistfucked','fistfucker','fistfuckers','fistfucking','fistfuckings','fistfucks','fitta','fitte','flange','flikker','fotze','ftq','fuck','fucked','fucker','fuckers','fuckin','fucking','fuckings','fuckmaster','fuckme','fucks','fuckwit','fucky','fuk','fuks','futkretzn','fux0r','gangbang','gangbanged','gangbangs','gash','gaysex','goddam','goddamn','goolies','guiena','h0r','h4x0r','hardcoresex','helvete','hoer','honkey','horniest','horny','hotsex','huevon','injun','jack-off','jerk-off','jism','jiz','jizm','kaffir','kawk','kike','knobend','knobhead','knobjockey','knulle','kock','kondum','kondums','kraut','kuk','kuksuger','kumer','kummer','kumming','kums','kunilingus','Kurac','kurwa','kusi','kyrp','lesbian','lesbo','lust','lusting','mamhoon','masturbat','merd','merde','mibun','milf','minge','minger','mong','monkleigh','mothafuck','mothafucka','mothafuckas','mothafuckaz','mothafucked','mothafucker','mothafuckers','mothafuckin','mothafucking','mothafuckings','mothafucks','motherfuck','motherfucked','motherfucker','motherfuckers','motherfuckin','motherfunkin','motherfucking','motherfuckings','motherfucks','mouliewop','muffdiver','muffmuncher','muie','mulkku','mummyporn','munter','muschi','nazis','nepesaurio','niger','nigga','niggar','niggars','nigger','niggers','nutsack','ootzak','orgasim','orgasims','orgasm','orgasms','orospu','paki','paska','pendejo','penis','penisperse','phonesex','phuck','phuk','phuked','phuking','phukked','phukking','phuks','phuq','picka','pierdol','pikey','pillu','pimmel','pimpis','pis','pises','pisin','pising','pisof','piss','pissed','pisser','pissers','pisses','pissin','pissing','pissoff','pizdapoontsee','porn','porno','pornography','pornos','pr0n','preteen','preud','prick','pricks','pula','pule','pusies','pusse','pussies','pussy','pussys','pusy','pusys','puta','puto','qaHbeh','queef','queer','quim','qweef','rautenbergschaffer','smut','spunk','scheiss','scheisse','schlampe','schmuck','scrotum','shag','shagged','sharmuta','sharmute','shemale','shenzi','shiat','shipal','shit','shited','shitfull','shithead','shithole','shiting','shitings','shits','shitted','shitter','shitters','shitting','shittings','shitty','shity','shiz','shizer','skribz','skurwysyn','slag','slut','sluts','smut','snatch','sodding','spacker','spacko','spank','spastic','spaz','sphencter','spierdalaj','splooge','spunk','spunking','suka','tits','titwank','tosser','turd','twat','twatty','uncunt','wank','wanked','wanker','wankered','wankers','wanking','wanky','whore','wog'];

}

// Error reporting
if (Config::SERVER == 'production') { // Turn off error reporting on production
    error_reporting(0);
}
else { // Turn on error reporting for staging
    error_reporting(E_ALL | E_STRICT);
    error_reporting(error_reporting() & ~E_NOTICE);
    ini_set("display_errors", 2);
}


define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

// Setup variables
$posts_response_count = 0;
$posts_noresponse_count = 0;
$questions_response_count = 0;
$questions_noresponse_count = 0;

$valid_filetypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'pages', 'key', 'numbers', 'jpeg', 'jpg', 'png', 'gif', 'bmp'];
$image_filetypes = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
$document_filetypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'pages', 'key', 'numbers'];
$word_filetypes = ['doc', 'docx'];
$powerpoint_filetypes = ['ppt', 'pptx'];
$excel_filetypes = ['xls', 'xlsx'];
$pdf_filetypes = ['pdf'];
$pages_filetypes = ['pages'];
$key_filetypes = ['key'];
$numbers_filetypes = ['numbers'];
