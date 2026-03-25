import zipfile
import os
import tkinter as tk
from tkinter import filedialog, messagebox, ttk


def zip_folder(folder_path, output_path, log_widget):
    folder_path = os.path.abspath(folder_path)
    count = 0
    with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(folder_path):
            for file in files:
                full_path = os.path.join(root, file)
                arcname = os.path.relpath(full_path, os.path.dirname(folder_path))
                zipf.write(full_path, arcname)
                log_widget.insert(tk.END, f"  + {arcname}\n")
                log_widget.see(tk.END)
                count += 1
    log_widget.insert(tk.END, f"\nSelesai! {count} file → {output_path}\n")
    log_widget.see(tk.END)


def pilih_folder():
    folder = filedialog.askdirectory(title="Pilih Folder yang Mau Di-zip")
    if folder:
        entry_folder.delete(0, tk.END)
        entry_folder.insert(0, folder)
        # Auto-isi nama output zip
        nama = os.path.basename(folder)
        entry_output.delete(0, tk.END)
        entry_output.insert(0, os.path.join(os.path.dirname(folder), nama + ".zip"))


def pilih_output():
    output = filedialog.asksaveasfilename(
        title="Simpan Zip Sebagai",
        defaultextension=".zip",
        filetypes=[("ZIP files", "*.zip")]
    )
    if output:
        entry_output.delete(0, tk.END)
        entry_output.insert(0, output)


def mulai_zip():
    folder = entry_folder.get().strip()
    output = entry_output.get().strip()

    if not folder or not os.path.isdir(folder):
        messagebox.showerror("Error", "Pilih folder yang valid!")
        return
    if not output:
        messagebox.showerror("Error", "Tentukan lokasi file output!")
        return

    log.delete(1.0, tk.END)
    btn_zip.config(state=tk.DISABLED)
    try:
        zip_folder(folder, output, log)
        messagebox.showinfo("Selesai", f"Zip berhasil dibuat:\n{output}")
    except Exception as e:
        messagebox.showerror("Error", str(e))
    finally:
        btn_zip.config(state=tk.NORMAL)


# ── GUI ────────────────────────────────────────────────────────
root = tk.Tk()
root.title("Zip Folder Tool")
root.resizable(False, False)

pad = {"padx": 8, "pady": 4}

tk.Label(root, text="Folder Input:").grid(row=0, column=0, sticky="w", **pad)
entry_folder = tk.Entry(root, width=55)
entry_folder.grid(row=0, column=1, **pad)
tk.Button(root, text="Browse", command=pilih_folder).grid(row=0, column=2, **pad)

tk.Label(root, text="Output ZIP:").grid(row=1, column=0, sticky="w", **pad)
entry_output = tk.Entry(root, width=55)
entry_output.grid(row=1, column=1, **pad)
tk.Button(root, text="Browse", command=pilih_output).grid(row=1, column=2, **pad)

btn_zip = tk.Button(root, text="Zip Sekarang", command=mulai_zip,
                    bg="#2563eb", fg="white", font=("Arial", 10, "bold"), padx=12)
btn_zip.grid(row=2, column=0, columnspan=3, pady=8)

log = tk.Text(root, width=70, height=15, state=tk.NORMAL, bg="#1e1e1e", fg="#d4d4d4",
              font=("Consolas", 9))
log.grid(row=3, column=0, columnspan=3, padx=8, pady=(0, 8))

root.mainloop()
