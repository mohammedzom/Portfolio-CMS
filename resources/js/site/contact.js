/** Global toast used for short confirmations such as "Email copied". */
export function toastStore() {
    let timer;

    return {
        message: '',
        visible: false,
        show(message) {
            this.message = message;
            this.visible = true;
            clearTimeout(timer);
            timer = setTimeout(() => {
                this.visible = false;
            }, 2200);
        },
    };
}

/** Copy the contact email, falling back to a hidden textarea outside secure contexts. */
export function copyEmail(email) {
    return {
        async copy() {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(email);
                } else {
                    fallbackCopy(email);
                }
            } catch {
                fallbackCopy(email);
            }

            this.$store.toast.show(`Email copied: ${email}`);
        },
    };
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
    } catch {
        // Nothing else to try; the address stays visible on the page.
    }

    textarea.remove();
}
