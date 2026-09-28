Option Explicit

Dim shell, fso
Dim base, php, runtimeDir
Dim stopFile
Dim serverCmd, workerCmd

Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

base = fso.GetParentFolderName(WScript.ScriptFullName)

php = base & "\php\php.exe"

runtimeDir = base & "\storage\app\private\ytclip-runtime"

stopFile = runtimeDir & "\stop.flag"

If Not fso.FolderExists(runtimeDir) Then
    fso.CreateFolder(runtimeDir)
End If


' ==========================================
' CEK INSTANCE YANG SUDAH BERJALAN
' ==========================================

If YtClipperRunning() Then
    shell.Run "http://127.0.0.1:8000", 1, False
    WScript.Quit
End If


' ==========================================
' HAPUS STOP FLAG LAMA
' ==========================================

If fso.FileExists(stopFile) Then
    fso.DeleteFile stopFile, True
End If


' ==========================================
' JALANKAN LARAVEL SERVER
' ==========================================

serverCmd = """" & php & """ artisan serve --host=127.0.0.1 --port=8000"

shell.Run serverCmd, 0, False


' ==========================================
' TUNGGU SERVER
' ==========================================

WScript.Sleep 3000


' ==========================================
' JALANKAN WORKER
' ==========================================

workerCmd = """" & php & """ artisan ytclip:worker"

shell.Run workerCmd, 0, False


' ==========================================
' BUKA BROWSER
' ==========================================

shell.Run "http://127.0.0.1:8000", 1, False


' ==========================================
' TUNGGU STOP FLAG
' ==========================================

Do

    WScript.Sleep 1000

    If fso.FileExists(stopFile) Then
        Exit Do
    End If

Loop


' ==========================================
' HENTIKAN SERVER DAN WORKER
' ==========================================

KillYtClipperProcesses


' ==========================================
' BERSIHKAN STOP FLAG
' ==========================================

If fso.FileExists(stopFile) Then
    fso.DeleteFile stopFile, True
End If

WScript.Quit


' ==========================================
' CEK INSTANCE YT CLIPPER
' ==========================================

Function YtClipperRunning()

    Dim service
    Dim processes
    Dim process
    Dim cmd

    YtClipperRunning = False

    Set service = GetObject("winmgmts:")

    Set processes = service.ExecQuery( _
        "SELECT ProcessId, CommandLine FROM Win32_Process WHERE Name='php.exe'" _
    )

    For Each process In processes

        cmd = LCase(process.CommandLine & "")

        If InStr(cmd, LCase(php)) > 0 Then

            If InStr(cmd, "artisan serve") > 0 _
            Or InStr(cmd, "artisan ytclip:worker") > 0 _
            Or InStr(cmd, "-s 127.0.0.1:8000") > 0 Then

                YtClipperRunning = True
                Exit Function

            End If

        End If

    Next

End Function


' ==========================================
' MATIKAN PROSES YT CLIPPER
' ==========================================

Sub KillYtClipperProcesses()

    Dim service
    Dim processes
    Dim process
    Dim cmd

    Set service = GetObject("winmgmts:")

    Set processes = service.ExecQuery( _
        "SELECT ProcessId, CommandLine FROM Win32_Process WHERE Name='php.exe'" _
    )

    For Each process In processes

        cmd = LCase(process.CommandLine & "")

        If InStr(cmd, LCase(php)) > 0 Then

            If InStr(cmd, "artisan serve") > 0 _
            Or InStr(cmd, "artisan ytclip:worker") > 0 _
            Or InStr(cmd, "-s 127.0.0.1:8000") > 0 Then

                shell.Run _
                    "cmd /c taskkill /PID " & process.ProcessId & " /T /F", _
                    0, _
                    True

            End If

        End If

    Next

End Sub
