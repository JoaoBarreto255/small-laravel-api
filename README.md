# Small Bank Api

## Endpoints
### POST /api/reset
Clear api data.

### GET /api/balance
Return integer balance from registred account.
If there's no account registry returns `0` and status code is 404.

### POST /api/event
Create/change user balance.
#### Types:
- **deposit**: create/increment user balance.
```json
# Expected
{
    "type": "deposit",
    "destination": "{user account id}",
    "amount": "{user balace change to make}"
}

# Output (Status 201)
{
    "destination": {
        "id": "{user account id}",
        "balance": # current user balance,
    }
}
```

- **withdraw**: update/decrement user balance.
```json
# Expected
{
    "type": "withdraw",
    "origin": "{user account id}",
    "amount": "{user balace change to make}"
}

# Output (Status 201)
{
    "origin": {
        "id": "{user account id}",
        "balance": # current user balance
    }
}
# Or (Status 404)
0
```
- **transfer**: move user balance part to another one.
```json
# Expected
{
    "type": "transfer",
    "origin": "{user account id}",
    "destination": "{2º user account id}",
    "amount": "{balance to moved}"
}

# Output (Status 201)
{
    "origin": {
        "id": "{user account id}",
        "balance": # current user balance
    },
    "destination": {
        "id": "{2º user account id}",
        "balance": # current user balance
    }
}

# Or (Status 404)
0
```
