; Script Inno Setup - Assistant de Consultation
; Pour générer le .exe : installer Inno Setup (gratuit, https://jrsoftware.org/isinfo.php),
; ouvrir ce fichier avec Inno Setup Compiler, puis cliquer sur "Compile".
; Le fichier AssistantConsultation_Setup.exe sera créé dans le dossier "Output".

#define MyAppName "Assistant de Consultation"
#define MyAppVersion "1.0"
#define MyAppExeName "demarrer_assistant.bat"

[Setup]
AppName={#MyAppName}
AppVersion={#MyAppVersion}
DefaultDirName=C:\AssistantConsultation
DefaultGroupName={#MyAppName}
DisableProgramGroupPage=yes
OutputBaseFilename=AssistantConsultation_Setup
Compression=lzma
SolidCompression=yes
ArchitecturesInstallIn64BitMode=x64
PrivilegesRequired=admin

[Languages]
Name: "french"; MessagesFile: "compiler:Languages\French.isl"

[Files]
; Le dossier "xampp_portable" doit contenir une version portable de XAMPP
; téléchargée depuis https://www.apachefriends.org puis placée à côté de ce script
; sous le nom "xampp_portable" avant compilation.
Source: "xampp_portable\*"; DestDir: "{app}\xampp"; Flags: ignoreversion recursesubdirs createallsubdirs
; Le code de l'application web
Source: "..\public\*"; DestDir: "{app}\xampp\htdocs\assistant-consultation\public"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "..\app\*"; DestDir: "{app}\xampp\htdocs\assistant-consultation\app"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "..\sql\*"; DestDir: "{app}\sql"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "demarrer_assistant.bat"; DestDir: "{app}"; Flags: ignoreversion
Source: "installer_base_donnees.bat"; DestDir: "{app}"; Flags: ignoreversion

[Icons]
Name: "{group}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"
Name: "{commondesktop}\{#MyAppName}"; Filename: "{app}\{#MyAppExeName}"; Tasks: desktopicon

[Tasks]
Name: "desktopicon"; Description: "Créer une icône sur le Bureau"; GroupDescription: "Icônes supplémentaires"

[Run]
Filename: "{app}\installer_base_donnees.bat"; Description: "Initialiser la base de données"; Flags: postinstall runascurrentuser shellexec
Filename: "{app}\{#MyAppExeName}"; Description: "Lancer l'Assistant de Consultation"; Flags: postinstall nowait skipifsilent shellexec
