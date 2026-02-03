# Real-World Examples

## Table of Contents

- [Real-World Examples](#real-world-examples)
  - [Table of Contents](#table-of-contents)
  - [Processing Form Input](#processing-form-input)
    - [Safe User Registration](#safe-user-registration)
    - [Contact Form Processing](#contact-form-processing)
  - [CSV/Database Import](#csvdatabase-import)
    - [Large CSV File Import](#large-csv-file-import)
    - [Database Migration with Data Transform](#database-migration-with-data-transform)

---

## Processing Form Input

### Safe User Registration

```php
class UserController {
    public function store(Request $request) {
        // Start with immutable for safety
        $input = JsArray::from($request->input('users'));

        // Filter out invalid entries
        $validated = $input
            ->filter(fn($user) => !empty($user['email']))
            ->filter(fn($user) => !empty($user['name']))
            ->map(fn($user) => [
                'email' => strtolower(trim($user['email'])),
                'name' => ucwords(trim($user['name'])),
                'created_at' => now(),
            ])
            ->filter(fn($user) =>
                filter_var($user['email'], FILTER_VALIDATE_EMAIL)
            );

        // Save validated data
        User::insert($validated->toArray());

        return response()->json([
            'total' => $input->length,
            'created' => $validated->length,
        ]);
    }
}
```

### Contact Form Processing

```php
$contacts = JsArray::from($_POST['contacts']);

$validContacts = $contacts
    ->map(fn($contact) => [
        'name' => trim($contact['name']),
        'email' => strtolower(trim($contact['email'])),
        'message' => trim($contact['message']),
    ])
    ->filter(fn($contact) =>
        !empty($contact['name']) &&
        !empty($contact['email']) &&
        !empty($contact['message'])
    )
    ->filter(fn($contact) =>
        filter_var($contact['email'], FILTER_VALIDATE_EMAIL)
    );

// Send emails
$validContacts->forEach(fn($contact) =>
    Mail::to($contact['email'])->send(
        new ContactReply($contact)
    )
);
```

---

## CSV/Database Import

### Large CSV File Import

```php
class CsvImporter {
    public function import(string $filename) {
        // Read file
        $fileHandle = fopen($filename, 'r');
        $header = fgetcsv($fileHandle);
        $rows = [];

        while ($row = fgetcsv($fileHandle)) {
            $rows[] = array_combine($header, $row);
        }
        fclose($fileHandle);

        // Process with MUTABLE mode (50K+ rows)
        $data = JsArray::mutable($rows)
            ->filter(fn($row) => !empty($row['id']))
            ->map(fn($row) => [
                'id' => (int)$row['id'],
                'name' => trim($row['name']),
                'email' => strtolower(trim($row['email'])),
                'phone' => trim($row['phone']),
                'imported_at' => now(),
            ])
            ->filter(fn($row) =>
                filter_var($row['email'], FILTER_VALIDATE_EMAIL)
            );

        // Batch insert for performance
        $batchSize = 1000;
        $batches = array_chunk($data->toArray(), $batchSize);

        foreach ($batches as $batch) {
            Contact::insert($batch);
        }

        return [
            'total_rows' => count($rows),
            'imported' => $data->length,
            'batches' => count($batches),
        ];
    }
}
```

### Database Migration with Data Transform

```php
class MigrateUserData {
    public function handle() {
        // Fetch old data
        $oldUsers = OldUserModel::all()->toArray();

        // Use mutable for speed (migration)
        $newUsers = JsArray::mutable($oldUsers)
            ->map(fn($user) => [
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => strtolower($user['email']),
                'status' => $user['active'] ? 'active' : 'inactive',
                'created_at' => $user['created_date'],
                'updated_at' => now(),
            ])
            ->filter(fn($user) =>
                filter_var($user['email'], FILTER_VALIDATE_EMAIL)
            );

        // Insert in batches
        $newUsers->forEach(fn($batch) => User::insert($batch));

        $this->info("Migrated {$newUsers->length} users");
    }
}
```
