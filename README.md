<p align="center"><img src="/art/banner.png" alt="Laravel Audit Log Queue"></p>

# Laravel Audit Log Queue

[![Latest Version](https://img.shields.io/github/release/ollico/laravel-audit-log-queue.svg?style=flat-square)](https://github.com/ollico/laravel-audit-log-queue/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Actions Status](https://github.com/ollico/laravel-audit-log-queue/workflows/run-tests/badge.svg)](https://github.com/ollico/laravel-audit-log-queue/actions)
[![Actions Status](https://github.com/ollico/laravel-audit-log-queue/workflows/cs-styling/badge.svg)](https://github.com/ollico/laravel-audit-log-queue/actions)

## A simple helper method to send activity logs to the queue.

Easily send auditable user (or any other entity) events to the queue and store them using the Activity Log by spatie.

The logger requires the use of an `Enum` class to ensure only allowed events are logged.

### Thanks
This package depends on the terrific [Activity Log](https://github.com/spatie/activitylog) package by Spatie.
