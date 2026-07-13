# Branching Strategy

## Purpose

Organize parallel development while maintaining code stability.

---

# Branch Types

main

Production.

develop

Ongoing development.

feature/*

New functionality.

fix/*

Bug fixes.

hotfix/*

Emergency production fixes.

release/*

Release preparation.

---

# Branch Lifecycle

develop

↓

feature

↓

Pull Request

↓

Code Review

↓

develop

↓

release

↓

main

---

# Rules

Never commit directly to main.

Every branch references an issue or task.

Delete merged branches after completion.