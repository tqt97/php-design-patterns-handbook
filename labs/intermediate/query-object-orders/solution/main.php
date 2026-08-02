<?php

declare(strict_types=1);

final readonly class OrderCriteria
{
	public function __construct(public ?string $status, public ?int $customerId, public string $sort = 'created_at_desc')
	{
	}
}
final class SearchOrders
{
	public function execute(OrderCriteria $criteria): array
	{
		return [['status' => $criteria->status, 'customer_id' => $criteria->customerId, 'sort' => $criteria->sort]];
	}
}
