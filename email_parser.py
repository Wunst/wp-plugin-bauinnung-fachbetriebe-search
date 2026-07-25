#!/usr/bin/env python3

'''
Parser for the email format returned by our initial signup form.
This will not be useful to you.
'''

import csv
import email
import email.message
import email.policy
import sys

def main() -> None:
    if len(sys.argv) < 3:
        print('usage: email_parser out_csv files_to_parse...')
        exit(1)
    
    outpath = sys.argv[1]
    inpaths = sys.argv[2:]

    outputs: list[dict[str, str]] = []


    logo_index = 0

    for path in inpaths:
        # Parse the email.
        msg: email.message.EmailMessage
        with open(path, 'r') as file:
            msg = email.message_from_file(file, policy=email.policy.default)


        # Get plain text.
        text = msg.get_body(('plain',))
        assert(text is not None)
        text = text.as_string()


        # Extract response data.
        lines = text.splitlines()
        data = dict(
                [line.split(':', 1) for line in lines if (':' in line)]
                )

        name = data['Name']
        address = f'{data['Strasse']}, {data['Ort']}, {data['PLZ']}'
        mail = data['Email']
        phone = data['Telefon']
        url = data['Url']

        categories = dict(
                # ';' instead of ',' in names as ',' is used as separator
                [(name.replace(';',','), '1') for name in data['Kategorien'].split(', ')]
                )


        # Extract logo to file.
        logo_filename: str | None = None

        for attachment in msg.iter_attachments():
            attachment_name = attachment.get_filename()
            if attachment_name is None:
                continue

            if attachment_name.endswith('.png'):
                logo_filename = f'logo{logo_index}.png'
                logo_index += 1
            elif attachment_name.endswith('.jpg') or name.endswith('.jpeg'):
                logo_filename = f'logo{logo_index}.jpg'
                logo_index += 1
            elif attachment_name.endswith('.gif'):
                logo_filename = f'logo{logo_index}.gif'
                logo_index += 1
            
            if logo_filename is not None:
                with open(logo_filename, '+bw') as logo_file:
                    logo_file.write(attachment.get_payload(decode=True))
                break


        outputs.append({
                'name': name,
                'address': address,
                'email': mail,
                'phone': phone,
                'url': url,
                'logo_url': f'/company_icons/{logo_filename}',
                } | categories)


    # Write data to CSV.
    with open(outpath, 'w') as outfile:
        fieldnames = set([key for row in outputs for key in row.keys()])

        csv_writer = csv.DictWriter(outfile, fieldnames=fieldnames)
        csv_writer.writeheader()
        csv_writer.writerows(outputs)


if __name__ == '__main__':
    main()
