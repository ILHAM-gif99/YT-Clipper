#define MyAppName "YT Clipper"
#define MyAppVersion "1.0.0"
#define MyAppPublisher "YT Clipper"
#define MyAppExeName "start.bat"

[Setup]
AppId={{8F8B4E71-4A6E-4A9B-9D6C-123456789ABC}}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
AppPublisher={#MyAppPublisher}

DefaultDirName={localappdata}\Programs\YT Clipper
DefaultGroupName={#MyAppName}

SetupIconFile=..\yt-clipper.ico
UninstallDisplayIcon={app}\yt-clipper.ico

OutputDir=.\output
OutputBaseFilename=YT-Clipper-Setup

PrivilegesRequired=lowest

Compression=lzma
SolidCompression=yes
WizardStyle=modern

[Files]
Source: "..\*"; DestDir: "{app}"; Flags: recursesubdirs createallsubdirs ignoreversion; Excludes: "installer\*;storage\app\private\ytclip-tmp\*"

[Icons]
Name: "{autodesktop}\YT Clipper"; Filename: "{sys}\wscript.exe"; Parameters: """{app}\YT-Clipper.vbs"""; WorkingDir: "{app}"; IconFilename: "{app}\yt-clipper.ico"
Name: "{group}\YT Clipper"; Filename: "{sys}\wscript.exe"; Parameters: """{app}\YT-Clipper.vbs"""; WorkingDir: "{app}"; IconFilename: "{app}\yt-clipper.ico"
Name: "{group}\Uninstall YT Clipper"; Filename: "{uninstallexe}"

[Run]
Filename: "{sys}\wscript.exe"; Parameters: """{app}\YT-Clipper.vbs"""; Description: "Jalankan YT Clipper"; Flags: nowait postinstall skipifsilent